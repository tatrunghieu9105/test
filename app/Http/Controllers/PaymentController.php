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
        if (!empty($data['discount_code'])) {
            $discount = \App\Models\DiscountCode::where('code', $data['discount_code'])->first();
        }
        $combo = null;
        if (!empty($data['combo_id'])) {
            $combo = \App\Models\Combo::find($data['combo_id']);
        }
        $numSeats = max(1, count($data['seat_ids']));
        $tickets = [];
        DB::transaction(function () use (&$tickets, $data, $discount, $combo, $numSeats) {
            foreach ($data['seat_ids'] as $seatId) {
                $price = \App\Models\Showtime::find($data['showtime_id'])->price;
                if ($discount) {
                    if ($discount->type === 'percent') {
                        $price = round($price * (100 - $discount->value) / 100, 2);
                    } else {
                        $price = max(0, $price - $discount->value);
                    }
                }
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
