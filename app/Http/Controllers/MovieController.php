<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->query('q');
        $categoryId = $request->query('category_id');

        $movies = Movie::query()
            ->with(['category'])
            ->search($keyword)
            ->byCategory($categoryId)
            ->orderByDesc('release_date')
            ->paginate(10)
            ->withQueryString();
            
        $categories = Category::all();

        return view('client.movies.index', compact('movies', 'keyword', 'categoryId', 'categories'));
    }

    public function show(Movie $movie)
    {
        $movie->load([
            'category', 
            'actors',
            'showtimes' => function ($q) {
                $q->upcoming()->with('room');
            }
        ]);

        // Debug log
        \Illuminate\Support\Facades\Log::info('Showtimes for movie ' . $movie->id, [
            'count' => $movie->showtimes->count(),
            'showtimes' => $movie->showtimes->toArray()
        ]);

        return view('client.movies.show', compact('movie'));
    }
}
