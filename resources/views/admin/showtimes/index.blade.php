@extends('admin.layout')

@section('title','Quản lý suất chiếu')
@section('page_title','Quản lý suất chiếu')

@section('content')
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="{{ route('admin.showtimes.create') }}">+ Tạo mới</a>
        <a class="btn {{ request('with_trashed') ? 'active' : '' }}" href="{{ route('admin.showtimes.index', ['with_trashed' => 1] + request()->except('with_trashed')) }}">Suất đã xóa</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phim</th>
                    <th>Phòng</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($showtimes as $st)
                <tr>
                    <td>{{ $st->id }}</td>
                    <td>{{ optional($st->movie)->title }}</td>
                    <td>{{ optional($st->room)->name }}</td>
                    <td>{{ $st->start_time }}</td>
                    <td>{{ $st->end_time }}</td>
                    <td>{{ number_format($st->price, 0, ',', '.') }} đ</td>
                    <td>
                        @if (!$st->deleted_at)
                            <a class="btn" href="{{ route('admin.showtimes.edit', $st) }}">Sửa</a>
                            <form class="inline" action="{{ route('admin.showtimes.destroy', $st) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn" onclick="return confirm('Xóa (mềm) suất chiếu này?')">Xóa</button>
                            </form>
                        @else
                            <form class="inline" action="{{ route('admin.showtimes.restore', $st->id) }}" method="post">
                                @csrf
                                <button class="btn">Khôi phục</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;">{{ $showtimes->links() }}</div>
@endsection


