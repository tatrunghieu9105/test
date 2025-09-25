<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function form()
    {
        return view('admin.checkin.form');
    }

    public function check(Request $request)
    {
        $data = $request->validate(['code' => 'required|string']);
        $codeInput = trim($data['code']);
        // Normalize: extract code from URL or query like ...?code=TKT-XXXX
        $code = $codeInput;
        if (str_contains($codeInput, 'http://') || str_contains($codeInput, 'https://')) {
            $parts = parse_url($codeInput);
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $qs);
                if (!empty($qs['code'])) { $code = trim($qs['code']); }
            }
            if ($code === $codeInput && !empty($parts['path'])) {
                $segments = array_values(array_filter(explode('/', $parts['path'])));
                if (!empty($segments)) { $code = trim(end($segments)); }
            }
        }
        $ticket = Ticket::with(['showtime'])->where('code', $code)->first();
        if (!$ticket) {
            return back()->with('error', 'Không tìm thấy vé.');
        }

        if (in_array($ticket->status, ['used'], true)) {
            return back()->with('error', 'Vé đã được sử dụng.');
        }

        if (!in_array($ticket->status, ['paid_cash','paid_online'], true)) {
            return back()->with('error', 'Chỉ check-in vé đã thanh toán.');
        }

        $now = now();
        if ($now->gt($ticket->showtime->end_time)) {
            return back()->with('error', 'Suất chiếu đã kết thúc.');
        }

        // Cho phép check-in bất kỳ lúc nào trước khi kết thúc suất chiếu
        $ticket->update(['status' => 'used', 'used_at' => $now]);
        return back()->with('success', 'Check-in thành công vé '.$ticket->code);
    }
}


