@extends('client.layout')

@section('title','Cập nhật tài khoản')

@section('content')
<h1 style="margin:0 0 12px 0">Cập nhật tài khoản</h1>

@if ($errors->any())
  <div class="card" style="border-left:4px solid #ef4444; margin-bottom:12px">{{ $errors->first() }}</div>
@endif

<div class="card" style="max-width:560px">
  <form method="post" action="{{ route('me.profile.update') }}">
    @csrf
    @method('PUT')
    <div class="form-row">
      <label>Họ tên</label>
      <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" required>
    </div>
    <div class="form-row">
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="form-row">
      <label>Mật khẩu mới (tuỳ chọn)</label>
      <input type="password" name="password" autocomplete="new-password" placeholder="Để trống nếu không đổi">
    </div>
    <div class="form-row">
      <label>Nhập lại mật khẩu</label>
      <input type="password" name="password_confirmation" autocomplete="new-password">
    </div>
    <div class="form-actions">
      <button class="btn" type="submit">Lưu thay đổi</button>
      <a class="btn btn-outline" href="{{ route('me.profile') }}">Hủy</a>
    </div>
  </form>
</div>
@endsection
