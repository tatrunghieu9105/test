<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketClientController extends Controller
{
    /**
     * Hiển thị danh sách các vé của người dùng hiện tại
     */
    public function index(Request $request)
    {
        $tickets = Ticket::with(['showtime.movie', 'showtime.room', 'seat'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('client.tickets.index', compact('tickets'));
    }

    /**
     * Hiển thị chi tiết một vé cụ thể
     */
    public function show(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === (auth()->id() ?? 0), 403);
        $ticket->load(['showtime.movie','showtime.room','seat']);
        // Generate QR data text (use code). For simplicity render as text; QR img can be added later
        $qrText = $ticket->code;
        return view('client.ticket_show', compact('ticket','qrText'));
    }
}


