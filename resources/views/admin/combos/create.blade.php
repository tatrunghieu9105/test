@extends('admin.layout')

@section('title','Tạo combo')
@section('page_title','Tạo combo')

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.combos.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-row">
                    <label>Tên</label>
                    <input type="text" name="name" placeholder="Ví dụ: Bắp + Nước" required>
                </div>
                <div class="form-row">
                    <label>Giá</label>
                    <input type="number" name="price" min="0" step="0.01" placeholder="Ví dụ: 50000" required>
                </div>
            </div>
            <div class="form-row">
                <label>Mô tả</label>
                <textarea name="description" rows="3" placeholder="Mô tả combo..."></textarea>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Lưu</button>
                <a class="btn" href="{{ route('admin.combos.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
@endsection
