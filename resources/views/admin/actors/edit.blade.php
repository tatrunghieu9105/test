@extends('admin.layout')

@section('title','Sửa diễn viên')
@section('page_title','Sửa diễn viên: '.$actor->name)

@section('content')
    <div class="card">
        <a href="{{ route('admin.actors.index') }}">← Danh sách</a>
        <h1>Sửa: {{ $actor->name }}</h1>
        <form method="post" action="{{ route('admin.actors.update', $actor) }}">
            @csrf
            @method('PUT')
            <label>Tên</label>
            <input type="text" name="name" value="{{ old('name', $actor->name) }}" required>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label>Ngày sinh</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $actor->birth_date) }}">
                </div>
            </div>
            <label>Tiểu sử</label>
            <textarea name="bio" rows="4">{{ old('bio', $actor->bio) }}</textarea>
            <button class="btn" type="submit">Cập nhật</button>
        </form>
    </div>
@endsection
