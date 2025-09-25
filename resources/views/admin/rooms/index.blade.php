@extends('admin.layout')

@section('title','Quản lý phòng')
@section('page_title','Quản lý phòng chiếu')

@section('content')
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="{{ route('admin.rooms.create') }}">+ Tạo mới</a>
        <a class="btn" href="{{ route('admin.rooms.index') }}">Chỉ đang hoạt động</a>
        <a class="btn" href="{{ route('admin.rooms.index', ['with_trashed' => 1]) }}">Bao gồm đã xóa</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Tổng ghế</th>
                    <th>Layout</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($rooms as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->total_seats }}</td>
                    <td>
                        @php $l = (array)($r->layout ?? []); @endphp
                        {{ ($l['rows'] ?? '?') }} x {{ ($l['cols'] ?? '?') }}
                    </td>
                    <td>{{ $r->deleted_at ? 'Đã xóa' : 'Hoạt động' }}</td>
                    <td>
                        @if (!$r->deleted_at)
                            <a class="btn" href="{{ route('admin.rooms.edit', $r) }}">Sửa</a>
                            <form class="inline" action="{{ route('admin.rooms.destroy', $r) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn" onclick="return confirm('Xóa (mềm) phòng này?')">Xóa</button>
                            </form>
                        @else
                            <form class="inline" action="{{ route('admin.rooms.restore', $r->id) }}" method="post">
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

    <div style="margin-top:12px;">{{ $rooms->links() }}</div>
@endsection


