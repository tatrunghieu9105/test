<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PointHistory;
use Illuminate\Http\Request;

class PointHistoryController extends Controller
{
    public function index(Request $request)
    {
        $histories = $request->user()
            ->pointHistories()
            ->with('ticket')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('client.points.history', compact('histories'));
    }
}
