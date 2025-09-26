<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
    $status = $request->query('status');
    $query = Ticket::withTrashed()->with(['user', 'showtime.movie', 'seat']);
    if ($status) { $query->where('status', $status); }
    $tickets = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
    return view('admin.tickets.index', compact('tickets', 'status'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'showtime.movie', 'showtime.room', 'seat', 'discount']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function markPaid(Ticket $ticket)
    {
        if ($ticket->status === 'used') {
            return back()->with('error', 'Vé đã sử dụng, không thể chuyển paid.');
        }
        if ($ticket->status === 'pending_cash') {
            DB::transaction(function () use ($ticket) {
                $ticket->update(['status' => 'paid_cash']);
                // Loyalty: award points + set tier
                \App\Services\LoyaltyService::awardForTicket($ticket->fresh());
            });
            return back()->with('success', 'Đã xác nhận thanh toán tại quầy.');
        }
        return back()->with('error', 'Chỉ xác nhận thanh toán cho vé tại quầy.');
// Đã thay thế logic phía trên, xóa code cũ tránh lỗi đóng ngoặc
    }

    public function markUsed(Ticket $ticket)
    {
        if (!in_array($ticket->status, ['paid_cash', 'paid_online'])) {
            return back()->with('error', 'Chỉ vé đã thanh toán mới được check-in.');
        }
        if ($ticket->used_at) {
            return back()->with('error', 'Vé đã được sử dụng.');
        }
        $ticket->update(['status' => 'used', 'used_at' => now()]);
        return back()->with('success', 'Đã check-in vé.');
    }

    public function cancel(Ticket $ticket)
    {
        if (!in_array($ticket->status, ['pending', 'pending_cash', 'pending_online'])) {
            return back()->with('error', 'Chỉ hủy vé ở trạng thái chưa thanh toán.');
        }
        // Chỉ giải phóng ghế nếu là vé online
        $ticket->status = 'cancelled';
        $ticket->save();
        if ($ticket->status === 'pending_online') {
            $ticket->delete(); // giải phóng ghế (ghế sẽ được đặt lại)
        }
        $msg = $ticket->status === 'pending_online' ? 'Đã hủy vé, ghế đã được giải phóng.' : 'Đã hủy vé.';
        return redirect()->route('admin.tickets.index')->with('success', $msg);
    }
}


