<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id() ?? 1; // demo
        $tickets = Ticket::withTrashed()->with(['showtime.movie', 'seat'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(12);
        return view('client.orders', compact('tickets'));
    }

    public function profile(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::find(1);
        return view('client.profile', compact('user'));
    }
}


