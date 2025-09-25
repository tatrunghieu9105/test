<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class UserTicketController extends Controller
{
    public function cancel(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }
        if ($ticket->status === 'paid_cash' || $ticket->status === 'paid_online') {
            return back()->with('error', 'Vé đã thanh toán thành công, không thể hủy.');
        }
        if (!in_array($ticket->status, ['pending_cash', 'pending_online'])) {
            return back()->with('error', 'Chỉ được hủy vé chưa thanh toán.');
        }
        $ticket->status = 'cancelled';
        $ticket->save();
        $ticket->delete(); // soft delete
        return redirect()->route('me.orders')->with('success', 'Đã hủy vé thành công.');
    }
}
