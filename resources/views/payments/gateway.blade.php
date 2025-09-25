<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Thanh toán {{ strtoupper($gateway) }}</title>
  <style> body{font-family:system-ui;margin:24px} .card{border:1px solid #e5e7eb;border-radius:8px;padding:16px;max-width:520px} a.button,button{padding:8px 10px;background:#111827;color:#fff;border-radius:6px;text-decoration:none;border:0} .muted{color:#6b7280} </style>
</head>
<body>
<h1>Giả lập {{ strtoupper($gateway) }}</h1>
<div class="card">
  <div><strong>Số tiền:</strong> {{ number_format($amount,0,',','.') }} đ</div>
  <div class="muted">Đây là trang giả lập cổng thanh toán. Chọn kết quả bên dưới.</div>
  @php $ids = implode(',', $ticket_ids); @endphp
  <div style="margin-top:12px;display:flex;gap:12px;">
    <a class="button" href="{{ route('payments.return', ['ticket_ids' => $ids, 'status' => 'success']) }}">Thanh toán thành công</a>
    <a class="button" style="background:#b91c1c" href="{{ route('payments.return', ['ticket_ids' => $ids, 'status' => 'fail']) }}">Thanh toán thất bại</a>
  </div>
</div>
</body>
</html>


