<?php

use App\Http\Controllers\UserTicketController;
Route::post('/me/tickets/{ticket}/cancel', [UserTicketController::class, 'cancel'])->name('me.tickets.cancel')->middleware('auth');

// Callback cho Momo và VNPay
use App\Http\Controllers\PaymentGatewayController;
Route::get('/payments/momo/callback', [PaymentGatewayController::class, 'momoCallback'])->name('payments.momo.callback');
Route::get('/payments/vnpay/callback', [PaymentGatewayController::class, 'vnpayCallback'])->name('payments.vnpay.callback');

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ActorController as AdminActorController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\ShowtimeController as AdminShowtimeController;
use App\Http\Controllers\Admin\ComboController as AdminComboController;
use App\Http\Controllers\Admin\DiscountCodeController as AdminDiscountController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\CheckinController as AdminCheckinController;
use App\Http\Controllers\Admin\StatsController as AdminStatsController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [MovieController::class, 'index'])->name('movies.index');

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');

// Booking flow
Route::get('/bookings', function() {
    // Chuyển hướng về trang chọn phim khi truy cập trực tiếp
    return redirect()->route('booking.select')->with('info', 'Vui lòng chọn phim và suất chiếu để đặt vé.');
});

// Trang chọn phim
Route::get('/bookings/select', [BookingController::class, 'select'])->name('booking.select');

// Trang chọn ghế
Route::get('/bookings/seats/{showtime}', [BookingController::class, 'create'])->name('bookings.seats');

// Xử lý đặt vé
Route::post('/bookings', [BookingController::class, 'store'])->name('booking.store');

// Trang chọn phương thức thanh toán
Route::get('/bookings/payment-method', [\App\Http\Controllers\BookingController::class, 'paymentMethod'])
    ->name('bookings.payment_method')
    ->middleware('auth');

// Xử lý thanh toán
use App\Http\Controllers\PaymentController;
Route::post('/payments/create', [PaymentController::class, 'create'])->middleware('auth')->name('payments.create');
Route::get('/payments/success', [PaymentController::class, 'success'])->name('payment.success');

// Authenticated user routes
Route::middleware(['auth'])->group(function(){
    // Lịch sử điểm thưởng
    Route::get('/me/points', [\App\Http\Controllers\Client\PointHistoryController::class, 'index'])
        ->name('me.points');
        
    // Profile routes
    Route::get('/me/profile', [\App\Http\Controllers\Client\ProfileController::class, 'show'])
        ->name('me.profile');
    Route::get('/me/profile/edit', [\App\Http\Controllers\Client\ProfileController::class, 'edit'])
        ->name('me.profile.edit');
    Route::put('/me/profile', [\App\Http\Controllers\Client\ProfileController::class, 'update'])
        ->name('me.profile.update');
    // Orders
    Route::get('/me/orders', [OrdersController::class, 'index'])->name('me.orders');
    Route::get('/me', [OrdersController::class, 'profile'])->name('me.profile');
    
    // Tickets
    Route::get('/me/tickets', [\App\Http\Controllers\TicketClientController::class, 'index'])->name('me.tickets');
    Route::get('/me/tickets/{ticket}', [\App\Http\Controllers\TicketClientController::class, 'show'])->name('me.tickets.show');
    // Profile
    Route::get('/me/profile', [ClientProfileController::class, 'show'])->name('me.profile');
    Route::get('/me/profile/edit', [ClientProfileController::class, 'edit'])->name('me.profile.edit');
    Route::put('/me/profile', [ClientProfileController::class, 'update'])->name('me.profile.update');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin (shared for both manager and staff)
Route::middleware(['role:manager,staff'])->group(function () {
    Route::get('/admin', function(){ return view('admin.dashboard'); })->name('admin.dashboard');

    // Rooms & seats (staff allowed)
    Route::get('/admin/rooms', [AdminRoomController::class, 'index'])->name('admin.rooms.index');
    Route::get('/admin/rooms/create', [AdminRoomController::class, 'create'])->name('admin.rooms.create');
    Route::post('/admin/rooms', [AdminRoomController::class, 'store'])->name('admin.rooms.store');
    Route::get('/admin/rooms/{room}/edit', [AdminRoomController::class, 'edit'])->name('admin.rooms.edit');
    Route::put('/admin/rooms/{room}', [AdminRoomController::class, 'update'])->name('admin.rooms.update');
    Route::post('/admin/rooms/{room}/layout', [AdminRoomController::class, 'updateLayout'])->name('admin.rooms.updateLayout');
    Route::delete('/admin/rooms/{room}', [AdminRoomController::class, 'destroy'])->name('admin.rooms.destroy');
    Route::post('/admin/rooms/{id}/restore', [AdminRoomController::class, 'restore'])->name('admin.rooms.restore');

    // Showtimes (staff allowed)
    Route::get('/admin/showtimes', [AdminShowtimeController::class, 'index'])->name('admin.showtimes.index');
    Route::get('/admin/showtimes/create', [AdminShowtimeController::class, 'create'])->name('admin.showtimes.create');
    Route::post('/admin/showtimes', [AdminShowtimeController::class, 'store'])->name('admin.showtimes.store');
    Route::get('/admin/showtimes/{showtime}/edit', [AdminShowtimeController::class, 'edit'])->name('admin.showtimes.edit');
    Route::put('/admin/showtimes/{showtime}', [AdminShowtimeController::class, 'update'])->name('admin.showtimes.update');
    Route::delete('/admin/showtimes/{showtime}', [AdminShowtimeController::class, 'destroy'])->name('admin.showtimes.destroy');
    Route::post('/admin/showtimes/{id}/restore', [AdminShowtimeController::class, 'restore'])->name('admin.showtimes.restore');

    // Tickets management (staff allowed)
    Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
    Route::get('/admin/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
    Route::post('/admin/tickets/{ticket}/mark-paid', [AdminTicketController::class, 'markPaid'])->name('admin.tickets.markPaid');
    Route::post('/admin/tickets/{ticket}/mark-used', [AdminTicketController::class, 'markUsed'])->name('admin.tickets.markUsed');
    Route::delete('/admin/tickets/{ticket}', [AdminTicketController::class, 'cancel'])->name('admin.tickets.cancel');

    // Check-in by code (staff allowed)
    Route::get('/admin/checkin', [AdminCheckinController::class, 'form'])->name('admin.checkin.form');
    Route::post('/admin/checkin', [AdminCheckinController::class, 'check'])->name('admin.checkin.check');
});

// Admin (manager only)
Route::middleware(['role:manager'])->group(function () {
    // Categories
    Route::get('/admin/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/admin/categories/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/admin/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/admin/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::post('/admin/categories/{id}/restore', [AdminCategoryController::class, 'restore'])->name('admin.categories.restore');

    // Actors
    Route::get('/admin/actors', [AdminActorController::class, 'index'])->name('admin.actors.index');
    Route::get('/admin/actors/create', [AdminActorController::class, 'create'])->name('admin.actors.create');
    Route::post('/admin/actors', [AdminActorController::class, 'store'])->name('admin.actors.store');
    Route::get('/admin/actors/{actor}/edit', [AdminActorController::class, 'edit'])->name('admin.actors.edit');
    Route::put('/admin/actors/{actor}', [AdminActorController::class, 'update'])->name('admin.actors.update');
    Route::delete('/admin/actors/{actor}', [AdminActorController::class, 'destroy'])->name('admin.actors.destroy');
    Route::post('/admin/actors/{id}/restore', [AdminActorController::class, 'restore'])->name('admin.actors.restore');

    // Movies
    Route::get('/admin/movies', [AdminMovieController::class, 'index'])->name('admin.movies.index');
    Route::get('/admin/movies/create', [AdminMovieController::class, 'create'])->name('admin.movies.create');
    Route::post('/admin/movies', [AdminMovieController::class, 'store'])->name('admin.movies.store');
    Route::get('/admin/movies/{movie}/edit', [AdminMovieController::class, 'edit'])->name('admin.movies.edit');
    Route::put('/admin/movies/{movie}', [AdminMovieController::class, 'update'])->name('admin.movies.update');
    Route::delete('/admin/movies/{movie}', [AdminMovieController::class, 'destroy'])->name('admin.movies.destroy');
    Route::post('/admin/movies/{id}/restore', [AdminMovieController::class, 'restore'])->name('admin.movies.restore');

    // Combos
    Route::get('/admin/combos', [AdminComboController::class, 'index'])->name('admin.combos.index');
    Route::get('/admin/combos/create', [AdminComboController::class, 'create'])->name('admin.combos.create');
    Route::post('/admin/combos', [AdminComboController::class, 'store'])->name('admin.combos.store');
    Route::get('/admin/combos/{combo}/edit', [AdminComboController::class, 'edit'])->name('admin.combos.edit');
    Route::put('/admin/combos/{combo}', [AdminComboController::class, 'update'])->name('admin.combos.update');
    Route::delete('/admin/combos/{combo}', [AdminComboController::class, 'destroy'])->name('admin.combos.destroy');
    Route::post('/admin/combos/{id}/restore', [AdminComboController::class, 'restore'])->name('admin.combos.restore');

    // Discount codes
    Route::get('/admin/discounts', [AdminDiscountController::class, 'index'])->name('admin.discounts.index');
    Route::get('/admin/discounts/create', [AdminDiscountController::class, 'create'])->name('admin.discounts.create');
    Route::post('/admin/discounts', [AdminDiscountController::class, 'store'])->name('admin.discounts.store');
    Route::get('/admin/discounts/{discount}/edit', [AdminDiscountController::class, 'edit'])->name('admin.discounts.edit');
    Route::put('/admin/discounts/{discount}', [AdminDiscountController::class, 'update'])->name('admin.discounts.update');
    Route::delete('/admin/discounts/{discount}', [AdminDiscountController::class, 'destroy'])->name('admin.discounts.destroy');
    Route::post('/admin/discounts/{id}/restore', [AdminDiscountController::class, 'restore'])->name('admin.discounts.restore');

    // Stats
    Route::get('/admin/stats', [AdminStatsController::class, 'index'])->name('admin.stats.index');

    // Audit Logs
    Route::get('/admin/logs', [AdminLogController::class, 'index'])->name('admin.logs.index');

    // Users & roles
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::put('/admin/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('admin.users.updateStatus');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // API: showtimes overlap check
    Route::get('/admin/showtimes/api', [AdminShowtimeController::class, 'api'])->name('admin.showtimes.api');
});

// Local helper for switching user role (dev only)
Route::get('/impersonate/{role}', function (string $role) {
    $user = \App\Models\User::whereHas('role', function($q) use ($role){
        $q->where('name', $role);
    })->first();
    if ($user) { auth()->login($user); }
    return redirect('/admin');
});
