@extends('client.layout')

@section('title', 'Đơn hàng của tôi')

@section('content')
<h1 style="margin:0 0 12px 0">Đơn hàng của tôi</h1>

<div class="card">
  @forelse ($tickets as $t)
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
          <div><strong>{{ number_format($t->price,0,',','.') }} đ</strong></div>
          <span class="badge" style="margin-top:4px">{{ $badge }}</span>
          <div style="margin-top:6px">
            @if($t->status !== 'cancelled')
              <a class="btn" href="{{ route('me.tickets.show', $t) }}">Xem vé</a>
            @endif
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="muted">Bạn chưa có vé nào.</div>
  @endforelse
</div>

<div style="margin-top:12px;">{{ $tickets->links() }}</div>
@endsection


