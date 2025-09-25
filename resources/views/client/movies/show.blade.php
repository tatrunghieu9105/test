@extends('client.layout')

@section('title', $movie->title)

@section('content')
<div class="row" style="align-items:flex-start">
  <div style="flex:0 0 280px">
    <img class="poster" src="{{ $movie->poster_url ?: 'https://picsum.photos/600/900?blur=3' }}" alt="{{ $movie->title }}">
  </div>
  <div style="flex:1">
    <h1 style="margin:0 0 6px 0">{{ $movie->title }}</h1>
    <div class="row" style="gap:8px; margin-bottom:6px">
      <span class="badge">{{ optional($movie->category)->name ?? '—' }}</span>
      @if($movie->release_date)
        <span class="badge">Phát hành: {{ $movie->release_date }}</span>
      @endif
      @if($movie->duration)
        <span class="badge">{{ $movie->duration }} phút</span>
      @endif
    </div>
    @if($movie->trailer_url)
      <p><a class="btn btn-outline" href="{{ $movie->trailer_url }}" target="_blank">Xem trailer</a></p>
    @endif
    <p class="muted" style="white-space:pre-line">{{ $movie->description }}</p>

    <div class="row" style="gap:6px; margin:10px 0 18px 0; flex-wrap:wrap">
      @forelse ($movie->actors as $actor)
        <span class="badge">{{ $actor->name }}</span>
      @empty
        <span class="muted">Chưa có diễn viên</span>
      @endforelse
    </div>

    <h3 style="margin:12px 0 8px 0">Suất chiếu sắp tới</h3>
    @php
      $grouped = collect($movie->showtimes)->groupBy(fn($st)=>\Carbon\Carbon::parse($st->start_time)->format('d/m/Y'));
    @endphp
    @forelse($grouped as $date => $items)
      <div class="card" style="margin-bottom:10px; padding:10px">
        <div class="row" style="justify-content:space-between; margin-bottom:8px">
          <strong>{{ $date }}</strong>
          <span class="muted">{{ count($items) }} suất</span>
        </div>
        <div class="row" style="gap:8px; flex-wrap:wrap">
          @foreach($items as $st)
            <div class="card" style="padding:8px; border-radius:10px; min-width:140px">
              <div class="muted" style="font-size:12px">Phòng {{ optional($st->room)->name }}</div>
              <div style="font-size:16px; font-weight:600">
                {{ \Carbon\Carbon::parse($st->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($st->end_time)->format('H:i') }}
              </div>
              <div class="muted" style="font-size:12px">
                {{ number_format($st->price,0,',','.') }} đ
              </div>
              <div style="margin-top:6px">
                @auth
                  <a class="btn" href="{{ route('bookings.seats', $st->id) }}">Chọn ghế</a>
                @else
                  <a class="btn btn-outline" href="{{ route('login') }}">Đăng nhập để đặt vé</a>
                @endauth
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="card"><span class="muted">Chưa có suất chiếu</span></div>
    @endforelse
  </div>
</div>

@endsection
