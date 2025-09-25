@extends('admin.layout')

@section('title','Sửa mã giảm giá')
@section('page_title','Sửa mã: '.$discount->code)

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.discounts.update', $discount) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-row">
                    <label>Mã</label>
                    <input type="text" name="code" value="{{ old('code', $discount->code) }}" required>
                </div>
                <div class="form-row">
                    <label>Loại</label>
                    <select name="type">
                        <option value="percent" @selected($discount->type==='percent')>Phần trăm</option>
                        <option value="amount" @selected($discount->type==='amount')>Số tiền</option>
                    </select>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Giá trị</label>
                    <input type="number" step="0.01" name="value" value="{{ old('value', $discount->value) }}" required>
                    <div class="hint">Nếu là phần trăm, nhập 10 cho 10%.</div>
                </div>
                <div class="form-row">
                    <label>Giá trị đơn hàng tối thiểu</label>
                    <input type="number" step="1000" name="min_order_value" value="{{ old('min_order_value', $discount->min_order_value) }}" placeholder="0 (không yêu cầu)">
                    <div class="hint">Đơn hàng phải đạt giá trị tối thiểu này để áp dụng mã. Để 0 nếu không yêu cầu.</div>
                </div>
                <div class="form-row">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $discount->start_date) }}">
                </div>
                <div class="form-row">
                    <label>Ngày kết thúc</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $discount->end_date) }}">
                </div>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Cập nhật</button>
                <a class="btn" href="{{ route('admin.discounts.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
@endsection
