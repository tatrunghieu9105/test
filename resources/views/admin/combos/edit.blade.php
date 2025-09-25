@extends('admin.layout')

@section('title','Sửa combo')
@section('page_title','Sửa combo: '.$combo->name)

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.combos.update', $combo) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-row">
                    <label>Tên</label>
                    <input type="text" name="name" value="{{ old('name', $combo->name) }}" required>
                </div>
                <div class="form-row">
                    <label>Giá</label>
                    <input type="number" name="price" min="0" step="0.01" value="{{ old('price', $combo->price) }}" required>
                </div>
            </div>
            <div class="form-row">
                <label>Mô tả</label>
                <textarea name="description" rows="3">{{ old('description', $combo->description) }}</textarea>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Cập nhật</button>
                <a class="btn" href="{{ route('admin.combos.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
@endsection
