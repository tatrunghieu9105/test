@extends('client.layout')

@section('title','Đăng nhập')

@section('content')
<h1 style="margin:0 0 12px 0">Đăng nhập</h1>

@if ($errors->any())
  <div class="login-alert error">
    <span>!</span>
    <span>{{ $errors->first() }}</span>
  </div>
@endif

@if (session('account_locked'))
  <div class="login-alert error">
    <span>!</span>
    <span>{{ session('account_locked') }}</span>
  </div>
@endif

<style>
  .login-alert {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    line-height: 1.5;
  }
  .login-alert span:first-child {
    font-weight: bold;
    font-size: 18px;
  }
</style>

<div class="card" style="max-width:420px">
<form method="POST" action="{{ route('login') }}" accept-charset="UTF-8">
    @csrf
    <div class="form-row">
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="form-row">
      <label>Mật khẩu</label>
      <input type="password" name="password" required>
    </div>
    <div class="form-row" style="flex-direction:row; align-items:center; gap:8px">
      <input type="checkbox" id="remember" name="remember">
      <label for="remember" class="muted">Ghi nhớ đăng nhập</label>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn">Đăng nhập</button>
      <a class="btn btn-outline" href="{{ route('register') }}">Đăng ký</a>
    </div>
  </form>
 </div>
 <div class="mt-12 pt-6 border-t border-gray-700 text-center">
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Cần hỗ trợ?</h4>
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm">
                        <a href="#" class="text-blue-400 hover:text-blue-300 flex items-center">
                            📞
                            0359445669
                        </a>
                        <span class="hidden sm:inline text-gray-600">•</span>
                        <a href="#" class="text-blue-400 hover:text-blue-300 flex items-center">
                            ✉️
                            hieuttph48854@gmail.com
                        </a>
                    </div>
                </div>
@endsection


