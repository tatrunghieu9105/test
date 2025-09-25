@extends('admin.layout')

@section('title','Sửa phim')
@section('page_title','Sửa phim: '.$movie->title)

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.movies.update', $movie) }}">
            @csrf
            @method('PUT')
            <label>Tiêu đề</label>
            <input type="text" name="title" value="{{ old('title', $movie->title) }}" required>
            <label>Mô tả</label>
            <textarea name="description" rows="4">{{ old('description', $movie->description) }}</textarea>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label>Thời lượng (phút)</label>
                    <input type="number" name="duration" value="{{ old('duration', $movie->duration) }}" min="1" required>
                </div>
                <div>
                    <label>Ngày phát hành</label>
                    <input type="date" name="release_date" value="{{ old('release_date', $movie->release_date) }}">
                </div>
            </div>
            <label>Danh mục</label>
            <select name="category_id">
                <option value="">-- Chưa chọn --</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $movie->category_id)==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <label>Diễn viên</label>
            <select name="actor_ids[]" multiple size="6">
                @php $selected = $movie->actors->pluck('id')->toArray(); @endphp
                @foreach($actors as $a)
                    <option value="{{ $a->id }}" @selected(in_array($a->id, old('actor_ids', $selected)))>{{ $a->name }}</option>
                @endforeach
            </select>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label>Poster URL</label>
                    <input type="url" name="poster_url" value="{{ old('poster_url', $movie->poster_url) }}">
                </div>
                <div>
                    <label>Trailer URL</label>
                    <input type="url" name="trailer_url" value="{{ old('trailer_url', $movie->trailer_url) }}">
                </div>
            </div>
            <button class="btn" type="submit">Cập nhật</button>
        </form>
    </div>
@endsection
