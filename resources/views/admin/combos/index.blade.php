@extends('admin.layout')

@section('title','Quản lý combo')
@section('page_title','Quản lý combo')

@section('content')
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="{{ route('admin.combos.create') }}">+ Tạo combo</a>
        <a class="btn" href="{{ route('admin.combos.index') }}">Chỉ đang hoạt động</a>
        <a class="btn" href="{{ route('admin.combos.index', ['with_trashed'=>1]) }}">Bao gồm đã xóa</a>
    </div>
    @if (session('success'))
        <div style="padding:8px;background:#ecfdf5;border:1px solid #10b981">{{ session('success') }}</div>
    @endif
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Giá</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            @foreach($combos as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ number_format($c->price,0,',','.') }} đ</td>
                    <td>{{ $c->description }}</td>
                    <td>{{ $c->deleted_at ? 'Đã xóa' : 'Hoạt động' }}</td>
                    <td>
                        @if(!$c->deleted_at)
                            <a class="btn" href="{{ route('admin.combos.edit', $c) }}">Sửa</a>
                            <form class="inline" action="{{ route('admin.combos.destroy', $c) }}" method="post">
                                @csrf @method('DELETE')
                                <button class="btn" onclick="return confirm('Xóa (mềm) combo này?')">Xóa</button>
                            </form>
                        @else
                            <form class="inline" action="{{ route('admin.combos.restore', $c->id) }}" method="post">
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
    <div style="margin-top:12px">{{ $combos->links() }}</div>
@endsection
