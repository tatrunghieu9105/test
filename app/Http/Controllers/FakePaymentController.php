<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;

class FakePaymentController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'exists:tickets,id',
            'gateway' => 'required|in:vnpay,momo',
        ]);

        $amount = (float) Ticket::whereIn('id', $data['ticket_ids'])->sum('price');
        $txn = [
            'gateway' => $data['gateway'],
            'amount' => $amount,
            'ticket_ids' => $data['ticket_ids'],
        ];

        // Simulate redirect to gateway page
        return view('payments.gateway', $txn);
    }

    public function return(Request $request)
    {
        $ticketIds = $request->query('ticket_ids');
        $status = $request->query('status', 'success');
        if (!$ticketIds) return redirect('/')->with('error', 'Thiếu tham số');
        $ids = array_map('intval', explode(',', $ticketIds));

        if ($status === 'success') {
            DB::transaction(function () use ($ids) {
                $tickets = Ticket::whereIn('id', $ids)->get();
                foreach ($tickets as $t) {
                    if ($t->status === 'pending') {
                        $t->status = 'paid';
                        $t->save();
                        if ($t->user) {
                            $pointsAdd = (int) floor($t->price / 10000);
                            $t->user->increment('points', $pointsAdd);
                            $user = $t->user->fresh();
                        }
                    }
                }
            });
            return redirect()->route('me.orders')->with('success', 'Thanh toán thành công');
        }

        return redirect()->route('me.orders')->with('error', 'Thanh toán thất bại');
    }
}


