@extends('admin.layout')

@section('title','Check-in')
@section('page_title','Check-in (QR/Barcode)')

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.checkin.check') }}">
            @csrf
            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap">
                <input type="text" name="code" placeholder="Quét hoặc nhập mã vé (ví dụ: TKT-XXXX)..." autofocus style="min-width:320px">
                <button class="btn" type="submit">Check-in</button>
            </div>
            <p style="color:var(--muted); margin-top:8px; font-size:13px">Tip: Có thể dán cả đường dẫn chứa mã (…?code=TKT-XXXX), hệ thống sẽ tự nhận diện.</p>
        </form>
    </div>
@endsection


