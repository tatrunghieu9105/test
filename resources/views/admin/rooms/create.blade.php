<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tạo phòng</title>
    <style> body{font-family:system-ui;margin:24px} input{width:100%;padding:8px;margin:4px 0} .row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px} </style>
</head>
<body>
<a href="{{ route('admin.rooms.index') }}">← Danh sách</a>
<h1>Tạo phòng</h1>

<form method="post" action="{{ route('admin.rooms.store') }}">
    @csrf
    <label>Tên</label>
    <input type="text" name="name" required>
    <div class="row">
        <div>
            <label>Số hàng</label>
            <input type="number" name="rows" min="1" max="26" required>
        </div>
        <div>
            <label>Số cột</label>
            <input type="number" name="cols" min="1" max="30" required>
        </div>
        <div>
            <label>Số hàng VIP cuối</label>
            <input type="number" name="vip_last_rows" min="0" max="26" value="0">
        </div>
    </div>
    <button type="submit">Lưu</button>
</form>

</body>
</html>


