<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    public function index(Request $request)
    {
        $withTrashed = (bool) $request->query('with_trashed', false);
        $query = Actor::query();
        if ($withTrashed) { $query->withTrashed(); }
        $actors = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.actors.index', compact('actors', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.actors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string',
            'birth_date' => 'nullable|date',
        ]);
        $actor = Actor::create($data);
        return redirect()->route('admin.actors.index')->with('success', 'Tạo diễn viên thành công');
    }

    public function edit(Actor $actor)
    {
        return view('admin.actors.edit', compact('actor'));
    }

    public function update(Request $request, Actor $actor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string',
            'birth_date' => 'nullable|date',
        ]);
        $actor->update($data);
        return redirect()->route('admin.actors.index')->with('success', 'Cập nhật diễn viên thành công');
    }

    public function destroy(Actor $actor)
    {
        if ($actor->movies()->exists()) {
            return back()->with('error', 'Không thể xóa diễn viên vì vẫn còn phim liên quan');
        }
        
        $actor->delete();
        return back()->with('success', 'Đã xóa diễn viên thành công');
    }

    public function restore(string $id)
    {
        $actor = Actor::withTrashed()->findOrFail($id);
        $actor->restore();
        return back()->with('success', 'Đã khôi phục');
    }
}


