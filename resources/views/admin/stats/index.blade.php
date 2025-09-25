@extends('admin.layout')

@section('title','Thống kê theo phim')
@section('page_title','Thống kê theo phim')

@section('content')
    <div class="card" style="margin-bottom:12px">
        <form method="get" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap">
            <div class="form-row">
                <label>Từ ngày</label>
                <input type="date" name="start" value="{{ $start }}">
            </div>
            <div class="form-row">
                <label>Đến ngày</label>
                <input type="date" name="end" value="{{ $end }}">
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Lọc</button>
                <a class="btn" href="{{ route('admin.stats.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Phim</th>
                    <th>Doanh thu</th>
                    <th>Số vé</th>
                    <th>Số suất</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($byMovie as $title => $data)
                <tr>
                    <td>{{ $title }}</td>
                    <td>{{ number_format($data['revenue'],0,',','.') }} đ</td>
                    <td>{{ $data['tickets'] }}</td>
                    <td>{{ $data['showtimes'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection


