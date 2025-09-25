<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'gateway' => 'required|in:cash,momo,vnpay',
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'discount_code' => 'nullable|string',
            'combo_id' => 'nullable|exists:combos,id',
        ]);

        // Kiểm tra ghế đã bị giữ chưa
        $takenSeatIds = \App\Models\Ticket::where('showtime_id', $data['showtime_id'])
            ->whereIn('status', ['pending_cash','paid_cash','paid_online','used'])
            ->pluck('seat_id')
            ->toArray();
        foreach ($data['seat_ids'] as $seatId) {
            if (in_array($seatId, $takenSeatIds)) {
                return back()->with('error', 'Ghế đã được đặt.');
            }
        }

        $discount = null;
        $discountAmount = 0;
        $totalAmount = 0;
        $showtime = null;
        
        if (!empty($data['discount_code']) || !empty($data['discount_amount'])) {
            // Lấy thông tin suất chiếu
            $showtime = \App\Models\Showtime::findOrFail($data['showtime_id']);
            
            // Tính tổng giá trị đơn hàng (số ghế * giá vé)
            $totalAmount = count($data['seat_ids']) * $showtime->price;
            
            if (!empty($data['discount_code'])) {
                // Kiểm tra mã giảm giá
                $discount = \App\Models\DiscountCode::where('code', $data['discount_code'])
                    ->where(function($query) use ($totalAmount) {
                        $query->where('min_order_value', '<=', $totalAmount)
                              ->orWhere('min_order_value', 0);
                    })
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();
                    
                if (!$discount) {
                    return back()->with('error', 'Mã khuyến mãi không hợp lệ, đã hết hạn hoặc không đủ điều kiện áp dụng.');
                }
                
                // Kiểm tra lại điều kiện giá trị đơn hàng tối thiểu
                if ($discount->min_order_value > 0 && $totalAmount < $discount->min_order_value) {
                    return back()->with('error', 'Đơn hàng tối thiểu phải đạt ' . number_format($discount->min_order_value) . ' VNĐ để sử dụng mã khuyến mãi này.');
                }
                
                // Tính toán số tiền giảm giá
                if ($discount->type === 'percent') {
                    $discountAmount = round($totalAmount * $discount->value / 100, 2);
                } else {
                    $discountAmount = min($discount->value, $totalAmount);
                }
            } elseif (!empty($data['discount_amount'])) {
                // Nếu có truyền trực tiếp số tiền giảm giá từ session
                $discountAmount = min($data['discount_amount'], $totalAmount);
            }
        }
        $combo = null;
        if (!empty($data['combo_id'])) {
            $combo = \App\Models\Combo::find($data['combo_id']);
        }
        $numSeats = max(1, count($data['seat_ids']));
        $tickets = [];
        
        // Nếu không có showtime từ trước, lấy lại
        if (!$showtime) {
            $showtime = \App\Models\Showtime::find($data['showtime_id']);
        }
        
        // Tính giá mỗi vé sau khi đã trừ đi phần giảm giá tương ứng
        $originalPrice = $showtime->price;
        $discountPerSeat = $discountAmount / $numSeats;
        
        DB::transaction(function () use (&$tickets, $data, $discount, $combo, $numSeats, $originalPrice, $discountPerSeat) {
            foreach ($data['seat_ids'] as $seatId) {
                $price = $originalPrice - $discountPerSeat;
                
                // Thêm giá combo nếu có
                if ($combo) {
                    $price = round($price + ($combo->price / $numSeats), 2);
                }
                $tickets[] = \App\Models\Ticket::create([
                    'user_id' => auth()->id() ?? 1,
                    'showtime_id' => $data['showtime_id'],
                    'seat_id' => $seatId,
                    'combo_id' => $combo?->id,
                    'discount_code_id' => $discount?->id,
                    'price' => $price,
                    'status' => $data['gateway'] === 'momo' || $data['gateway'] === 'vnpay' ? 'pending_online' : 'pending_cash',
                ]);
            }
        });
        $amount = collect($tickets)->sum('price');

        // Gửi email xác nhận nếu có user và email
        $user = auth()->user();
        $email = $user && $user->email ? $user->email : $request->input('email');
        if ($email) {
            Mail::to($email)->send(new \App\Mail\TicketPaidMail($tickets, $amount));
            session()->flash('success', 'Đặt vé thành công! Vui lòng kiểm tra email để xem thông tin vé.');
        }

        // Lưu ticket_ids vào session để callback cập nhật trạng thái vé
        $ticketIds = collect($tickets)->pluck('id')->toArray();
        session(['ticket_ids' => $ticketIds]);

        if ($data['gateway'] === 'cash') {
            // Lấy thông tin chi tiết của các vé đã tạo
            $ticketDetails = [];
            foreach ($tickets as $ticket) {
                $ticketDetails[] = \App\Models\Ticket::with(['showtime.movie', 'showtime.room', 'seat'])
                    ->find($ticket->id);
            }
            
            // Lưu thông tin đơn hàng vào session để hiển thị trên trang xác nhận
            session([
                'payment_success' => [
                    'tickets' => $ticketDetails,
                    'amount' => $amount,
                    'payment_method' => 'Tiền mặt',
                    'message' => 'Đặt vé thành công! Vui lòng thanh toán tại quầy.'
                ]
            ]);
            
            // Chuyển hướng đến trang xác nhận
            return redirect()->route('payment.success')->with('success', 'Đặt vé thành công! Vui lòng thanh toán tại quầy.');
        }

        // Online payment: chuyển hướng sang gateway
        if ($data['gateway'] === 'momo') {
            return app(\App\Http\Controllers\PaymentGatewayController::class)->momo(new Request([
                'amount' => $amount
            ]));
        }
        if ($data['gateway'] === 'vnpay') {
            return app(\App\Http\Controllers\PaymentGatewayController::class)->vnpay(new Request([
                'amount' => $amount
            ]));
        }
        abort(400, 'Phương thức thanh toán không hợp lệ.');
    }

    /**
     * Hiển thị trang xác nhận thanh toán thành công
     */
    public function success()
    {
        if (!session()->has('payment_success')) {
            return redirect()->route('movies.index');
        }

        $paymentData = session('payment_success');
        
        // Xóa dữ liệu thanh toán khỏi session sau khi đã lấy
        session()->forget('payment_success');
        
        // Chuyển đổi mảng tickets thành Collection
        $tickets = collect($paymentData['tickets']);
        
        // Lấy mã đơn hàng từ ticket đầu tiên (nếu có)
        $firstTicket = $tickets->first();
        $orderCode = $firstTicket ? $firstTicket->code : 'N/A';
        
        return view('client.payments.success', [
            'tickets' => $tickets,
            'amount' => $paymentData['amount'],
            'payment_method' => $paymentData['payment_method'],
            'message' => $paymentData['message'],
            'order_code' => $orderCode
        ]);
    }
}
