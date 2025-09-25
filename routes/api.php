<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserStatus;

// API lấy danh sách ghế cho 1 suất chiếu
Route::get('/showtimes/{showtime}/seats', function($showtimeId) {
    $showtime = \App\Models\Showtime::with('room')->findOrFail($showtimeId);
    $takenSeatIds = \App\Models\Ticket::where('showtime_id', $showtimeId)
        ->whereIn('status', ['pending_cash','paid_cash','paid_online','used'])
        ->pluck('seat_id')
        ->toArray();
    $seats = \App\Models\Seat::where('room_id', $showtime->room_id)
        ->orderBy('code')
        ->get()
        ->map(function ($seat) use ($takenSeatIds) {
            return [
                'id' => $seat->id,
                'code' => $seat->code,
                'type' => $seat->type,
                'is_taken' => in_array($seat->id, $takenSeatIds),
            ];
        });
    return response()->json(['seats' => $seats]);
});

// API kiểm tra trạng thái tài khoản
// Route kiểm tra trạng thái tài khoản
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/account/status', [AccountStatusController::class, 'check']);
});
