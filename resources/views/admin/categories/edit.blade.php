@extends('admin.layout')

@section('title','Sửa thể loại')
@section('page_title','Sửa: '.$category->name)

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')
            <div class="form-row">
                <label>Tên</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
            </div>
            <div class="form-row">
                <label>Mô tả</label>
                <textarea name="description" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Cập nhật</button>
                <a class="btn" href="{{ route('admin.categories.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
@endsection


