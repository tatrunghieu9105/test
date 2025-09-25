@extends('client.layout')

@section('title', 'Chọn suất chiếu')

@section('content')
<h1 style="margin:0 0 12px 0">Chọn suất chiếu - {{ $movie->title }}</h1>

@php
  $grouped = collect($showtimes)->groupBy(fn($st)=>\Carbon\Carbon::parse($st->start_time)->format('d/m/Y'));
@endphp

@forelse($grouped as $date => $items)
  <div class="card" style="margin-bottom:12px">
    <div class="row" style="justify-content:space-between; margin-bottom:8px">
      <strong>{{ $date }}</strong>
      <span class="muted">{{ count($items) }} suất</span>
    </div>
    <div class="row" style="gap:10px; flex-wrap:wrap">
      @foreach($items as $st)
        <div class="card" style="padding:10px; border-radius:10px">
          <div class="muted" style="font-size:12px">Phòng {{ optional($st->room)->name }}</div>
          <div style="font-size:16px; font-weight:600">{{ \Carbon\Carbon::parse($st->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($st->end_time)->format('H:i') }}</div>
          <div class="muted" style="font-size:12px">{{ number_format($st->price,0,',','.') }} đ</div>
          <div style="margin-top:6px">
            <a class="btn" href="{{ route('booking.seats', $st) }}">Chọn ghế</a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
@empty
  <div class="card"><span class="muted">Chưa có suất chiếu</span></div>
@endforelse
@endsection


