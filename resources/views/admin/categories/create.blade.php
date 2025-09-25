@extends('admin.layout')

@section('title','Tạo thể loại')
@section('page_title','Tạo thể loại')

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="form-row">
                <label>Tên</label>
                <input type="text" name="name" placeholder="Ví dụ: Hành động" required>
            </div>
            <div class="form-row">
                <label>Mô tả</label>
                <textarea name="description" rows="3" placeholder="Mô tả ngắn..."></textarea>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Lưu</button>
                <a class="btn" href="{{ route('admin.categories.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
@endsection


