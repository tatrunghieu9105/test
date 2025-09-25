<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Actor;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $withTrashed = (bool) $request->query('with_trashed', false);
        $query = Movie::query()->with(['category']);
        if ($withTrashed) { $query->withTrashed(); }
        $movies = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        return view('admin.movies.index', compact('movies', 'withTrashed'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $actors = Actor::orderBy('name')->get();
        return view('admin.movies.create', compact('categories', 'actors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'duration' => 'required|integer|min:1',
            'release_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'actor_ids' => 'array',
            'actor_ids.*' => 'exists:actors,id',
        ]);

        $movie = Movie::create($data);
        $movie->actors()->sync($request->input('actor_ids', []));
        return redirect()->route('admin.movies.index')->with('success', 'Tạo phim thành công');
    }

    public function edit(Movie $movie)
    {
        $categories = Category::orderBy('name')->get();
        $actors = Actor::orderBy('name')->get();
        $movie->load('actors');
        return view('admin.movies.edit', compact('movie', 'categories', 'actors'));
    }

    public function update(Request $request, Movie $movie)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'duration' => 'required|integer|min:1',
            'release_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'actor_ids' => 'array',
            'actor_ids.*' => 'exists:actors,id',
        ]);

        $movie->update($data);
        $movie->actors()->sync($request->input('actor_ids', []));
        return redirect()->route('admin.movies.index')->with('success', 'Cập nhật phim thành công');
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();
        return back()->with('success', 'Đã xóa (mềm)');
    }

    public function restore(string $id)
    {
        $movie = Movie::withTrashed()->findOrFail($id);
        $movie->restore();
        return back()->with('success', 'Đã khôi phục');
    }
}


