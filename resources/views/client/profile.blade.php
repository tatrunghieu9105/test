@extends('client.layout')

@section('title', 'Tài khoản')

@section('content')
<h1 style="margin:0 0 12px 0">Tài khoản</h1>

@if (session('success'))
  <div class="card" style="border-left:4px solid #10b981; margin-bottom:12px">{{ session('success') }}</div>
@endif

<div class="card" style="max-width:560px">
  <div class="row" style="justify-content:space-between">
    <div>
      <div class="muted" style="font-size:13px">Họ tên</div>
      <div style="margin-bottom:8px"><strong>{{ $user->fullname }}</strong></div>
      <div class="muted" style="font-size:13px">Email</div>
      <div style="margin-bottom:8px">{{ $user->email }}</div>
      <div class="muted" style="font-size:13px">Điểm</div>
      <div style="margin-bottom:8px">{{ $user->points }}</div>
    </div>
    <div>
      <a class="btn" href="{{ route('me.profile.edit') }}" style="display:inline-block;margin-bottom:8px">Cập nhật tài khoản</a>
      <a class="btn btn-outline" href="/me/orders">Xem đơn hàng</a>
    </div>
  </div>
</div>
@endsection


