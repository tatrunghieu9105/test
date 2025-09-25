@extends('admin.layout')

@section('title','Chi tiết vé')
@section('page_title')
    <div class="flex justify-between items-center">
        <div>Chi tiết vé #{{ $ticket->id }}</div>
@endsection

@php
  $movie = optional(optional($ticket->showtime)->movie);
  $room  = optional(optional($ticket->showtime)->room);
  $start = optional($ticket->showtime?->start_time)?->format('d/m/Y H:i');
  $end   = optional($ticket->showtime?->end_time)?->format('H:i');
  $badge = match($ticket->status){
    'pending_cash' => 'Chưa thanh toán (quầy)',
    'pending_online' => 'Chưa thanh toán (online)',
    'paid_cash' => 'Đã thanh toán (quầy)',
    'paid_online' => 'Đã thanh toán (online)',
    'used' => 'Đã sử dụng',
    'cancelled' => 'Đã hủy',
    default => $ticket->status,
  };
@endphp

@section('content')
  <div class="card" style="max-width:880px">
    <div class="row" style="justify-content:space-between; align-items:flex-start">
      <div>
        <div class="muted" style="font-size:13px">Mã vé</div>
        <div style="margin-bottom:8px"><strong>{{ $ticket->code }}</strong></div>
        <div class="muted" style="font-size:13px">Khách</div>
        <div style="margin-bottom:8px">{{ optional($ticket->user)->fullname }} ({{ optional($ticket->user)->email }})</div>
        <div class="muted" style="font-size:13px">Phim</div>
        <div style="margin-bottom:8px">{{ $movie->title ?? '—' }}</div>
        <div class="muted" style="font-size:13px">Phòng • Suất</div>
        <div style="margin-bottom:8px">{{ $room->name ?? '—' }} • {{ $start }} - {{ $end }}</div>
        <div class="muted" style="font-size:13px">Ghế</div>
        <div style="margin-bottom:8px">{{ optional($ticket->seat)->code }}</div>
      </div>
      <div style="text-align:right">
        <div class="muted" style="font-size:13px">Giá</div>
        <div><strong>{{ number_format($ticket->price,0,',','.') }} đ</strong></div>
        <div style="margin-top:6px"><span class="badge">{{ $badge }}</span></div>
        @if($ticket->used_at)
          <div class="muted" style="margin-top:6px;font-size:12px">Used at: {{ \Carbon\Carbon::parse($ticket->used_at)->format('d/m/Y H:i') }}</div>
        @endif
        @if($ticket->discount)
          <div class="muted" style="margin-top:6px;font-size:12px">Mã giảm: {{ $ticket->discount->code }}</div>
        @endif
      </div>
    </div>
  </div>

  <div class="row" style="gap:8px; margin-top:12px">
    @if ($ticket->status==='pending_cash')
        <form action="{{ route('admin.tickets.markPaid', $ticket) }}" method="post" class="inline">@csrf<button class="btn">Xác nhận đã thanh toán</button></form>
        <form action="{{ route('admin.tickets.cancel', $ticket) }}" method="post" class="inline">@csrf @method('DELETE')<button class="btn" onclick="return confirm('Hủy vé?')">Hủy</button></form>
    @elseif ($ticket->status==='paid_cash')
        <form action="{{ route('admin.tickets.markUsed', $ticket) }}" method="post" class="inline">@csrf<button class="btn">Check-in</button></form>
    @elseif ($ticket->status==='pending_online')
        <form action="{{ route('admin.tickets.cancel', $ticket) }}" method="post" class="inline">@csrf @method('DELETE')<button class="btn" onclick="return confirm('Hủy vé?')">Hủy</button></form>
    @elseif ($ticket->status==='paid_online')
        <form action="{{ route('admin.tickets.markUsed', $ticket) }}" method="post" class="inline">@csrf<button class="btn">Check-in</button></form>
    @endif
    <a href="{{ route('admin.tickets.index') }}" class="btn">Quay lại</a>
  </div>
@endsection


