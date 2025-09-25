<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowtimeController extends Controller
{
    public function index(Request $request)
{
    $withTrashed = (bool) $request->query('with_trashed', false);
    $query = Showtime::query()->with(['movie', 'room']);
    
    if ($withTrashed) { 
        $query->withTrashed(); 
    }
    
    $showtimes = $query->orderByDesc('start_time')->paginate(12)->withQueryString();
    
    return view('admin.showtimes.index', compact('showtimes', 'withTrashed'));
}

    public function create()
    {
        $movies = Movie::orderBy('title')->get();
        $rooms = Room::orderBy('name')->get();
        return view('admin.showtimes.create', compact('movies', 'rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'price' => 'required|numeric|min:0',
        ]);

        $overlap = Showtime::where('room_id', $data['room_id'])
            ->whereNull('deleted_at')
            ->where(function($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function($q2) use ($data){
                      $q2->where('start_time', '<=', $data['start_time'])
                         ->where('end_time', '>=', $data['end_time']);
                  });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['start_time' => 'Suất chiếu trùng thời gian trong cùng phòng'])->withInput();
        }

        $showtime = Showtime::create($data);
        return redirect()->route('admin.showtimes.index')->with('success', 'Tạo suất chiếu thành công');
    }

    public function edit(Showtime $showtime)
    {
        $movies = Movie::orderBy('title')->get();
        $rooms = Room::orderBy('name')->get();
        return view('admin.showtimes.edit', compact('showtime', 'movies', 'rooms'));
    }

    public function update(Request $request, Showtime $showtime)
    {
        $data = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'price' => 'required|numeric|min:0',
        ]);

        $overlap = Showtime::where('room_id', $data['room_id'])
            ->where('id', '!=', $showtime->id)
            ->whereNull('deleted_at')
            ->where(function($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function($q2) use ($data){
                      $q2->where('start_time', '<=', $data['start_time'])
                         ->where('end_time', '>=', $data['end_time']);
                  });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['start_time' => 'Suất chiếu trùng thời gian trong cùng phòng'])->withInput();
        }

        $showtime->update($data);
        return redirect()->route('admin.showtimes.index')->with('success', 'Cập nhật suất chiếu thành công');
    }

    public function destroy(Showtime $showtime)
    {
        $showtime->delete();
        return back()->with('success', 'Đã xóa (mềm) suất chiếu');
    }

    public function restore(string $id)
    {
        $st = Showtime::withTrashed()->findOrFail($id);
        $st->restore();
        return back()->with('success', 'Đã khôi phục suất chiếu');
    }

    // Lightweight API for front-end validation
    public function api(Request $request)
{
    $data = $request->validate([
        'room_id' => 'required|exists:rooms,id',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
        'exclude_id' => 'nullable|integer'
    ]);

    $q = Showtime::where('room_id', $data['room_id'])
        ->whereNull('deleted_at')
        ->where(function($q) use ($data) {
            $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
              ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
              ->orWhere(function($q2) use ($data) {
                  $q2->where('start_time', '<=', $data['start_time'])
                     ->where('end_time', '>=', $data['end_time']);
              });
        });

    if (!empty($data['exclude_id'])) {
        $q->where('id', '!=', $data['exclude_id']);
    }

    $overlap = $q->exists();
    
    return response()->json([
        'overlap' => $overlap,
        'message' => $overlap ? 'Phòng chiếu đã có suất chiếu trong khoảng thời gian này' : 'Thời gian hợp lệ'
    ]);
}
}


