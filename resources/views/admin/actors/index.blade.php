@extends('admin.layout')

@section('title','Quản lý diễn viên')
@section('page_title','Quản lý diễn viên')

@section('content')
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="{{ route('admin.actors.create') }}">+ Tạo diễn viên</a>
        <a class="btn" href="{{ route('admin.actors.index') }}">Chỉ đang hoạt động</a>
        <a class="btn" href="{{ route('admin.actors.index', ['with_trashed'=>1]) }}">Bao gồm đã xóa</a>
        <a class="btn" href="{{ route('admin.dashboard') }}" style="margin-left:auto;">← Dashboard</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Ngày sinh</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            @foreach($actors as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>{{ $a->name }}</td>
                    <td>{{ $a->birth_date }}</td>
                    <td>{{ $a->deleted_at ? 'Đã xóa' : 'Hoạt động' }}</td>
                    <td>
                        @if(!$a->deleted_at)
                            <a class="btn" href="{{ route('admin.actors.edit', $a) }}">Sửa</a>
                            <form class="inline" action="{{ route('admin.actors.destroy', $a) }}" method="post">
                                @csrf @method('DELETE')
                                <button class="btn" onclick="return confirm('Xóa (mềm) diễn viên này?')">Xóa</button>
                            </form>
                        @else
                            <form class="inline" action="{{ route('admin.actors.restore', $a->id) }}" method="post">
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

    <div style="margin-top:12px">{{ $actors->links() }}</div>
@endsection
