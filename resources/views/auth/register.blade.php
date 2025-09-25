@extends('client.layout')

@section('title','Đăng ký')

@section('content')
<h1 style="margin:0 0 12px 0">Đăng ký</h1>

@if ($errors->any())
  <div class="card" style="border-left:4px solid #ef4444; margin-bottom:12px">{{ $errors->first() }}</div>
@endif

<div class="card" style="max-width:420px">
  <form method="post" action="{{ route('register') }}">
    @csrf
    <div class="form-row">
      <label>Họ tên</label>
      <input type="text" name="fullname" value="{{ old('fullname') }}" required>
    </div>
    <div class="form-row">
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div class="form-row">
      <label>Mật khẩu</label>
      <input type="password" name="password" required>
    </div>
    <div class="form-row">
      <label>Nhập lại mật khẩu</label>
      <input type="password" name="password_confirmation" required>
    </div>
    <div class="form-actions">
      <button class="btn" type="submit">Đăng ký</button>
      <a class="btn btn-outline" href="{{ route('login') }}">Đăng nhập</a>
    </div>
  </form>
 </div>
@endsection


