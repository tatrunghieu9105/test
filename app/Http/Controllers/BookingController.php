<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\DiscountCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Hiển thị trang chọn phim và suất chiếu
     */
    public function select(Request $request)
    {
        $movies = Movie::with(['showtimes' => function($query) {
            $query->where('start_time', '>', now())
                  ->orderBy('start_time');
        }, 'showtimes.room'])
        ->whereHas('showtimes', function($query) {
            $query->where('start_time', '>', now());
        })
        ->get();

        $combos = \App\Models\Combo::orderBy('price')->get();
        
        $discountCodes = DiscountCode::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->get();

        // Chuẩn bị dữ liệu showtimesByMovie cho JS
        $showtimesByMovie = [];
        foreach ($movies as $movie) {
            $showtimesByMovie[$movie->id] = $movie->showtimes->map(function($st) {
                return [
                    'id' => $st->id,
                    'room_name' => optional($st->room)->name,
                    'start_time' => $st->start_time->format('H:i'),
                    'end_time' => $st->end_time->format('H:i'),
                    'formatted_date' => $st->start_time->format('d/m/Y'),
                    'formatted_time' => $st->start_time->format('H:i') . ' - ' . $st->end_time->format('H:i')
                ];
            })->toArray();
        }

        return view('client.bookings.select', compact('movies', 'combos', 'discountCodes', 'showtimesByMovie'));
    }

    /**
     * Hiển thị trang chọn ghế
     */
    public function create($showtimeId, Request $request)
    {
        $showtime = Showtime::with(['room', 'movie'])->findOrFail($showtimeId);
        $room = $showtime->room;
        
        // Lấy danh sách ghế đã đặt
        $bookedSeatIds = Ticket::where('showtime_id', $showtime->id)
            ->whereIn('status', ['pending_cash', 'pending_online', 'paid_cash', 'paid_online', 'used'])
            ->pluck('seat_id')
            ->toArray();

        // Lấy danh sách ghế trong phòng
        $seats = Seat::where('room_id', $room->id)
            ->orderBy('code')
            ->get()
            ->map(function($seat) use ($bookedSeatIds) {
                return (object) [
                    'id' => $seat->id,
                    'code' => $seat->code,
                    'type' => $seat->type,
                    'is_taken' => in_array($seat->id, $bookedSeatIds)
                ];
            });

        $combos = \App\Models\Combo::orderBy('price')->get();
        
        $discountCodes = DiscountCode::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->get();
            
        // Lấy thông báo lỗi từ session nếu có
        if ($request->session()->has('error')) {
            $errors = new \Illuminate\Support\MessageBag();
            $errors->add('discount_code', $request->session()->get('error'));
            view()->share('errors', $errors);
        }

        return view('client.bookings.seats', [
            'showtime' => $showtime,
            'room' => $room,
            'seats' => $seats,
            'combos' => $combos,
            'discountCodes' => $discountCodes,
        ]);
    }

    /**
     * Xử lý đặt vé
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'discount_code' => 'nullable|string',
            'combo_id' => 'nullable|exists:combos,id',
        ]);

        try {
            $user = auth()->user();
            $showtime = Showtime::with(['movie', 'room'])->findOrFail($validated['showtime_id']);
            
            // Kiểm tra xem các ghế đã được đặt chưa
            $existingTickets = Ticket::where('showtime_id', $showtime->id)
                ->whereIn('seat_id', $validated['seat_ids'])
                ->exists();
                
            if ($existingTickets) {
                return back()->with('error', 'Một số ghế đã được đặt. Vui lòng chọn ghế khác.');
            }

            // Tính toán tổng tiền
            $totalAmount = $showtime->price * count($validated['seat_ids']);
            $discountAmount = 0;
            $discountCode = null;

            // Kiểm tra mã giảm giá nếu có
            if (!empty($validated['discount_code'])) {
                $discount = DiscountCode::where('code', $validated['discount_code'])
                    ->where('is_active', true)
                    ->where(function($q) use ($totalAmount) {
                        $q->where('min_order_value', '<=', $totalAmount)
                          ->orWhere('min_order_value', 0);
                    })
                    ->where(function($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    })
                    ->first();

                if ($discount) {
                    // Kiểm tra lại điều kiện giá trị đơn hàng tối thiểu
                    if ($discount->min_order_value > 0 && $totalAmount < $discount->min_order_value) {
                        return back()->with('error', 'Đơn hàng tối thiểu phải đạt ' . number_format($discount->min_order_value) . ' VNĐ để sử dụng mã khuyến mãi này.');
                    }
                    
                    $discountAmount = $discount->type === 'percent' 
                        ? round($totalAmount * $discount->value / 100, 2)
                        : $discount->value;
                    
                    if ($discountAmount > $totalAmount) {
                        $discountAmount = $totalAmount;
                    }
                    
                    $discountCode = $discount->code;
                } else {
                    return back()->with('error', 'Mã khuyến mãi không hợp lệ, đã hết hạn hoặc không đủ điều kiện áp dụng.');
                }
            }

            // Tính tổng tiền cuối cùng
            $finalAmount = max(0, $totalAmount - $discountAmount);
            
            // Tính giá mỗi vé sau khi áp dụng giảm giá
            $numSeats = count($validated['seat_ids']);
            $seatPrice = $showtime->price;
            $totalDiscountPerSeat = $discountAmount / $numSeats;
            
            // Lưu thông tin vào session để sử dụng ở bước thanh toán
            $bookingData = [
                'showtime_id' => $showtime->id,
                'seat_ids' => $validated['seat_ids'],
                'total_amount' => $totalAmount,
                'discount_code' => $discountCode,
                'discount_amount' => $discountAmount,
                'discount_per_seat' => $totalDiscountPerSeat, // Lưu số tiền giảm giá cho mỗi vé
                'final_amount' => $finalAmount,
                'combo_id' => $validated['combo_id'] ?? null,
            ];
            
            session(['booking_data' => $bookingData]);
            
            // Chuyển hướng đến trang chọn phương thức thanh toán
            return redirect()->route('bookings.payment_method');
            
            // Cập nhật điểm tích lũy cho người dùng
            $pointsEarned = (int)($finalAmount / 10000); // 1 điểm cho mỗi 10,000đ
            $user->increment('points', $pointsEarned);
            
            // Lưu lịch sử tích điểm
            if (Schema::hasTable('point_histories')) {
                DB::table('point_histories')->insert([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'points' => $pointsEarned,
                    'type' => 'earn',
                    'description' => 'Tích điểm từ đặt vé phim ' . $showtime->movie->title,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Gửi email xác nhận
            if ($user->email) {
                Mail::to($user->email)->send(new \App\Mail\TicketBooked($order, $tickets));
            }

            DB::commit();

            // Chuyển hướng đến trang xác nhận
            return redirect()->route('booking.confirmation', $order->id)
                ->with('success', 'Đặt vé thành công! Bạn đã nhận được ' . $pointsEarned . ' điểm tích lũy.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi đặt vé: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'showtime_id' => $validated['showtime_id'] ?? null,
                'seat_ids' => $validated['seat_ids'] ?? []
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi đặt vé. Vui lòng thử lại sau.');
        }
    }

    /**
     * Hiển thị trang chọn phương thức thanh toán
     */
    public function paymentMethod()
    {
        $bookingData = session('booking_data');
        
        if (!$bookingData) {
            return redirect()->route('booking.select')->with('error', 'Vui lòng chọn ghế trước khi thanh toán.');
        }
        
        $showtime = Showtime::with(['movie', 'room'])->findOrFail($bookingData['showtime_id']);
        $seats = Seat::whereIn('id', $bookingData['seat_ids'])->get();
        $combo = $bookingData['combo_id'] ? \App\Models\Combo::find($bookingData['combo_id']) : null;
        
        return view('client.bookings.payment_method', [
            'showtime' => $showtime,
            'seats' => $seats,
            'combo' => $combo,
            'total_amount' => $bookingData['total_amount'],
            'discount_amount' => $bookingData['discount_amount'],
            'final_amount' => $bookingData['final_amount'],
            'discount_code' => $bookingData['discount_code'],
            'showtime_id' => $bookingData['showtime_id'],
            'seat_ids' => $bookingData['seat_ids'],
            'combo_id' => $bookingData['combo_id']
        ]);
    }

    /**
     * Hiển thị trang xác nhận đặt vé thành công
     */
    public function confirmation($orderId)
    {
        $order = Order::with(['tickets.seat', 'showtime.movie', 'showtime.room'])
            ->where('id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('client.bookings.confirmation', compact('order'));
    }
}
