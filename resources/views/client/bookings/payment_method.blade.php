@extends('client.layout')

@section('title', 'Chọn phương thức thanh toán')

@section('content')
<h1 style="margin:0 0 12px 0">Chọn phương thức thanh toán</h1>
@php
    $showtime = \App\Models\Showtime::with(['movie','room'])->find($showtime_id);
    $combo = $combo_id ? \App\Models\Combo::find($combo_id) : null;
    $discount = $discount_code ? \App\Models\DiscountCode::where('code', $discount_code)->first() : null;
    $numSeats = count($seat_ids);
    $basePrice = $showtime ? $showtime->price : 0;
    $subtotal = $basePrice * $numSeats;
    $discountAmount = 0;
    if ($discount) {
        $discountAmount = $discount->type === 'percent' ? round($subtotal * $discount->value / 100, 2) : $discount->value;
        if ($discountAmount > $subtotal) $discountAmount = $subtotal;
    }
    $comboAmount = $combo?->price ?? 0;
    $total = max(0, $subtotal - $discountAmount) + $comboAmount;
@endphp

<div class="row" style="gap:16px; align-items:flex-start">
  <form class="card" method="post" action="{{ route('payments.create') }}" style="flex:1">
      @csrf
      <input type="hidden" name="showtime_id" value="{{ $showtime_id }}">
      @foreach($seat_ids as $id)
          <input type="hidden" name="seat_ids[]" value="{{ $id }}">
      @endforeach
      @if($discount_code)
          <input type="hidden" name="discount_code" value="{{ $discount_code }}">
      @endif
      @if($combo_id)
          <input type="hidden" name="combo_id" value="{{ $combo_id }}">
      @endif

      <h3 style="margin-top:0">Phương thức</h3>
      <label class="row" style="gap:8px"><input type="radio" name="gateway" value="cash" checked> Thanh toán tiền mặt tại quầy</label>
      <label class="row" style="gap:8px"><input type="radio" name="gateway" value="momo"> Thanh toán qua MoMo</label>
      <label class="row" style="gap:8px"><input type="radio" name="gateway" value="vnpay"> Thanh toán qua VNPay</label>

      <div class="row" style="margin-top:12px">
        <button type="submit" class="btn">Xác nhận thanh toán</button>
      </div>
  </form>

  <div class="card" style="min-width:280px">
      <h3 style="margin-top:0">Tóm tắt đơn</h3>
      <div class="muted" style="font-size:14px">Phim: {{ optional($showtime->movie)->title }}</div>
      <div class="muted" style="font-size:14px">Thời gian: {{ $showtime && $showtime->start_time ? \Carbon\Carbon::parse($showtime->start_time)->format('d/m/Y H:i') : '—' }}</div>
      <div class="muted" style="font-size:14px">Phòng: {{ optional($showtime->room)->name }}</div>
      <div class="muted" style="font-size:14px">Số ghế: {{ $numSeats }}</div>
      <div style="border-top:1px solid var(--border); margin:10px 0"></div>
      <div class="row" style="justify-content:space-between"><span>Tạm tính</span><strong>{{ number_format($subtotal, 0, ',', '.') }} đ</strong></div>
      <div class="row" style="justify-content:space-between"><span>Giảm giá</span><strong>-{{ number_format($discountAmount, 0, ',', '.') }} đ</strong></div>
      <div class="row" style="justify-content:space-between"><span>Combo</span><strong>{{ number_format($comboAmount, 0, ',', '.') }} đ</strong></div>
      <div class="row" style="justify-content:space-between"><span>Tổng</span><strong style="color:#22c55e">{{ number_format($total, 0, ',', '.') }} đ</strong></div>
  </div>
</div>
@endsection
