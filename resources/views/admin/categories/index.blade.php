@extends('admin.layout')

@section('title','Quản lý thể loại')
@section('page_title','Quản lý thể loại')

@section('content')
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="{{ route('admin.categories.create') }}">+ Tạo mới</a>
        <a class="btn" href="{{ route('admin.categories.index') }}">Chỉ đang hoạt động</a>
        <a class="btn" href="{{ route('admin.categories.index', ['with_trashed' => 1]) }}">Bao gồm đã xóa</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($categories as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->description }}</td>
                    <td>{{ $c->deleted_at ? 'Đã xóa' : 'Hoạt động' }}</td>
                    <td>
                        @if (!$c->deleted_at)
                            <a class="btn" href="{{ route('admin.categories.edit', $c) }}">Sửa</a>
                            <form class="inline" action="{{ route('admin.categories.destroy', $c) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn" onclick="return confirm('Xóa (mềm) thể loại này?')">Xóa</button>
                            </form>
                        @else
                            <form class="inline" action="{{ route('admin.categories.restore', $c->id) }}" method="post">
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

    <div style="margin-top:12px;">{{ $categories->links() }}</div>
@endsection
