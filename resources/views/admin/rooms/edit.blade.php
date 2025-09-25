<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sửa phòng</title>
    <style>
        body{font-family:system-ui;margin:24px}
        input{width:100%;padding:8px;margin:4px 0}
        .grid{display:grid;grid-template-columns:repeat(10,40px);gap:6px;margin-top:12px}
        .seat{width:40px;height:40px;line-height:40px;text-align:center;border:1px solid #e5e7eb;border-radius:6px}
        .vip{background:#fde68a}
    </style>
</head>
<body>
<a href="{{ route('admin.rooms.index') }}">← Danh sách</a>
<h1>Sửa phòng: {{ $room->name }}</h1>

<form method="post" action="{{ route('admin.rooms.update', $room) }}">
    @csrf
    @method('PUT')
    <label>Tên</label>
    <input type="text" name="name" value="{{ old('name', $room->name) }}" required>
    <button type="submit">Cập nhật</button>
</form>

@php $layout = (array)($room->layout ?? []); @endphp
<h3>Layout ghế</h3>
<form method="post" action="{{ route('admin.rooms.updateLayout', $room) }}">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;max-width:600px">
        <div>
            <label>Số hàng</label>
            <input type="number" name="rows" value="{{ old('rows', $layout['rows'] ?? 5) }}" min="1" max="26" required>
        </div>
        <div>
            <label>Số cột</label>
            <input type="number" name="cols" value="{{ old('cols', $layout['cols'] ?? 8) }}" min="1" max="30" required>
        </div>
        <div>
            <label>Số hàng VIP cuối</label>
            <input type="number" name="vip_last_rows" value="{{ old('vip_last_rows', $layout['vip_last_rows'] ?? 0) }}" min="0" max="26">
        </div>
    </div>
    <button type="submit" style="margin-top:8px;">Cập nhật layout</button>
</form>

<div class="grid">
@foreach ($room->seats as $s)
    <div class="seat {{ $s->type==='VIP' ? 'vip' : '' }}" title="{{ $s->code }}">{{ $s->code }}</div>
@endforeach
</div>

</body>
</html>


