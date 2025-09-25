@extends('client.layout')

@section('title', 'Tóm tắt đặt vé')

@section('content')
<h1 style="margin:0 0 12px 0">Tóm tắt đặt vé</h1>

<div class="card" style="margin-bottom:12px">
  @foreach ($tickets as $t)
    @php
      $movie = optional(optional($t->showtime)->movie);
      $room = optional(optional($t->showtime)->room);
      $time = optional($t->showtime)->start_time;
      $badge = match($t->status){
        'pending_cash' => 'Chưa thanh toán (quầy)',
        'pending_online' => 'Chưa thanh toán (online)',
        'paid_cash' => 'Đã thanh toán (quầy)',
        'paid_online' => 'Đã thanh toán (online)',
        'used' => 'Đã sử dụng',
        'cancelled' => 'Đã hủy',
        default => $t->status,
      };
    @endphp
    <div class="card" style="margin-bottom:8px">
      <div class="row" style="justify-content:space-between">
        <div>
          <div><strong>#{{ $t->code }}</strong> • {{ $movie->title ?? '—' }}</div>
          <div class="muted" style="font-size:13px">Phòng {{ $room->name ?? '—' }} • {{ $time }}</div>
          <div class="muted" style="font-size:13px">Ghế: {{ optional($t->seat)->code }}</div>
        </div>
        <div style="text-align:right">
          <div><strong>{{ number_format($t->price, 0, ',', '.') }} đ</strong></div>
          <span class="badge" style="margin-top:4px">{{ $badge }}</span>
        </div>
      </div>
    </div>
  @endforeach

  <div class="row" style="justify-content:flex-end; border-top:1px solid var(--border); padding-top:8px">
    <div><strong>Tổng: {{ number_format($total, 0, ',', '.') }} đ</strong></div>
  </div>
</div>

@php $allPendingCash = collect($tickets)->every(fn($t) => $t->status === 'pending_cash'); @endphp
@if($allPendingCash)
  <div class="card" style="border-left:4px solid #10b981">Vé đã được giữ. Vui lòng thanh toán tại quầy để nhận vé.</div>
  <a class="btn btn-outline" href="/me/orders" style="margin-top:8px;">Xem đơn hàng</a>
@else
  <form class="card" method="post" action="{{ route('payments.create') }}" style="margin-top:12px;">
      @csrf
      @foreach ($tickets as $t)
          <input type="hidden" name="ticket_ids[]" value="{{ $t->id }}">
      @endforeach
      <div style="margin-bottom:12px;">
          <label class="row" style="gap:8px"><input type="radio" name="gateway" value="momo" {{ request('gateway')==='momo'?'checked':'' }}> Thanh toán qua MoMo</label>
          <label class="row" style="gap:8px"><input type="radio" name="gateway" value="vnpay" {{ request('gateway')==='vnpay'?'checked':'' }}> Thanh toán qua VNPay</label>
      </div>
      <div class="row" style="gap:8px">
        <button type="submit" class="btn">Thanh toán</button>
        <a class="btn btn-outline" href="/movies">Tiếp tục xem phim</a>
      </div>
  </form>
@endif
@endsection


