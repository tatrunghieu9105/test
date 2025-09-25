<!DOCTYPE html>
<html>
<body style="font-family:system-ui">
<h2>Cảm ơn bạn đã đặt vé</h2>
<p>Chi tiết vé:</p>
<ul>
@foreach ($tickets as $t)
    <li>
        Mã: {{ $t->code }} | Suất: {{ optional($t->showtime)->start_time }} | Ghế: {{ optional($t->seat)->code }} | Giá: {{ number_format($t->price,0,',','.') }} đ
        <br>
        <span>QR vé:</span><br>
        @php
            $qrData = $t->code; // Chỉ chứa mã vé
        @endphp
        {!! QrCode::size(120)->generate($qrData) !!}
    </li>
@endforeach
</ul>
<p><strong>Tổng:</strong> {{ number_format($total,0,',','.') }} đ</p>
<p>Chúc bạn xem phim vui vẻ!</p>
</body>
</html>


