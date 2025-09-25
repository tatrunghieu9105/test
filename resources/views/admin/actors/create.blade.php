@extends('admin.layout')

@section('title','Tạo diễn viên')
@section('page_title','Tạo diễn viên')

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.actors.store') }}">
            @csrf
            <label>Tên</label>
            <input type="text" name="name" required>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label>Ngày sinh</label>
                    <input type="date" name="birth_date">
                </div>
            </div>
            <label>Tiểu sử</label>
            <textarea name="bio" rows="4"></textarea>
            <button class="btn" type="submit">Lưu</button>
        </form>
    </div>
@endsection
