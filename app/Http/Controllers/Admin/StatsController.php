<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->query('start', now()->subDays(6)->toDateString());
        $end = $request->query('end', now()->toDateString());

        $rows = Ticket::select('showtime_id', DB::raw('SUM(price) as revenue'), DB::raw('COUNT(*) as tickets'))
            ->whereBetween(DB::raw('DATE(created_at)'), [$start, $end])
            ->whereIn('status', ['paid','used'])
            ->groupBy('showtime_id')
            ->with('showtime.movie')
            ->get();

        $byMovie = [];
        foreach ($rows as $r) {
            $movie = optional($r->showtime->movie)->title ?? 'N/A';
            if (!isset($byMovie[$movie])) $byMovie[$movie] = ['revenue' => 0, 'tickets' => 0, 'showtimes' => 0];
            $byMovie[$movie]['revenue'] += (float)$r->revenue;
            $byMovie[$movie]['tickets'] += (int)$r->tickets;
            $byMovie[$movie]['showtimes'] += 1;
        }

        uasort($byMovie, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        return view('admin.stats.index', [
            'start' => $start,
            'end' => $end,
            'byMovie' => $byMovie,
        ]);
    }
}


