@extends('admin.layout')

@section('title','Quản lý phim')
@section('page_title','Quản lý phim')

@section('content')
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="{{ route('admin.movies.create') }}">+ Tạo phim</a>
        <a class="btn" href="{{ route('admin.movies.index') }}">Chỉ đang hoạt động</a>
        <a class="btn" href="{{ route('admin.movies.index', ['with_trashed'=>1]) }}">Bao gồm đã xóa</a>
        <a class="btn" href="{{ route('admin.dashboard') }}" style="margin-left:auto;">← Dashboard</a>
    </div>


    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Thời lượng</th>
                    <th>Ngày phát hành</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            @foreach($movies as $m)
                <tr>
                    <td>{{ $m->id }}</td>
                    <td>{{ $m->title }}</td>
                    <td>{{ $m->category->name ?? '—' }}</td>
                    <td>{{ $m->duration }}'</td>
                    <td>{{ $m->release_date }}</td>
                    <td>{{ $m->deleted_at ? 'Đã xóa' : 'Hoạt động' }}</td>
                    <td>
                        @if(!$m->deleted_at)
                            <a class="btn" href="{{ route('admin.movies.edit', $m) }}">Sửa</a>
                            <form class="inline" action="{{ route('admin.movies.destroy', $m) }}" method="post">
                                @csrf @method('DELETE')
                                <button class="btn" onclick="return confirm('Xóa (mềm) phim này?')">Xóa</button>
                            </form>
                        @else
                            <form class="inline" action="{{ route('admin.movies.restore', $m->id) }}" method="post">
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

    <div style="margin-top:12px">{{ $movies->links() }}</div>
@endsection
