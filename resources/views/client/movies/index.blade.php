@extends('client.layout')

@section('title', 'Danh sách phim')

@section('content')
<h1 style="margin:0 0 12px 0">Đang chiếu</h1>

<form method="get" class="row" style="margin:12px 0;gap:12px">
    <input type="text" name="q" value="{{ $keyword ?? '' }}" placeholder="Tìm theo tên...">
    <select name="category_id">
        <option value="">Tất cả thể loại</option>
        @foreach(($categories ?? []) as $c)
            <option value="{{ $c->id }}" @selected(($categoryId ?? '')==$c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn">Lọc</button>
    <a href="{{ route('movies.index') }}" class="btn btn-outline">Reset</a>
    @if(isset($keyword) && $keyword !== '')
      <span class="badge">Từ khóa: {{ $keyword }}</span>
    @endif
  </form>

<div class="grid">
    @isset($movies)
    @foreach ($movies as $movie)
      <div class="card">
        <img class="poster" src="{{ $movie->poster_url ?: 'https://picsum.photos/400/600?blur=3' }}" alt="{{ $movie->title }}">
        <div class="row" style="justify-content:space-between; margin-top:8px">
          <span class="badge">{{ optional($movie->category)->name ?? '—' }}</span>
          <span class="muted">{{ $movie->release_date }}</span>
        </div>
        <h3 style="margin:8px 0 6px 0; font-size:16px">{{ $movie->title }}</h3>
        <div class="row" style="justify-content:space-between">
          <a class="btn" href="{{ route('movies.show', $movie) }}">Chi tiết</a>
        </div>
      </div>
    @endforeach
    @endisset
  </div>

  @isset($movies)
      @if($movies instanceof \Illuminate\Pagination\LengthAwarePaginator)
          <div style="margin-top:16px;">
              {{ $movies->links() }}
          </div>
      @endif
  @endisset
@endsection


