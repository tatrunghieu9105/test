@extends('admin.layout')

@section('title','Quản lý mã giảm giá')
@section('page_title','Quản lý mã giảm giá')

@section('content')
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="{{ route('admin.discounts.create') }}">+ Tạo mới</a>
        <a class="btn" href="{{ route('admin.discounts.index') }}">Chỉ đang hoạt động</a>
        <a class="btn" href="{{ route('admin.discounts.index', ['with_trashed' => 1]) }}">Bao gồm đã xóa</a>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Mã</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Hiệu lực</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($discounts as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td>{{ $d->code }}</td>
                    <td>{{ $d->type }}</td>
                    <td>{{ $d->type==='percent' ? $d->value.'%' : number_format($d->value,0,',','.') .' đ' }}</td>
                    <td>{{ $d->start_date }} → {{ $d->end_date }}</td>
                    <td>{{ $d->deleted_at ? 'Đã xóa' : 'Hoạt động' }}</td>
                    <td>
                        @if (!$d->deleted_at)
                            <a class="btn" href="{{ route('admin.discounts.edit', $d) }}">Sửa</a>
                            <form class="inline" action="{{ route('admin.discounts.destroy', $d) }}" method="post">
                                @csrf @method('DELETE')
                                <button class="btn" onclick="return confirm('Xóa (mềm) mã này?')">Xóa</button>
                            </form>
                        @else
                            <form class="inline" action="{{ route('admin.discounts.restore', $d->id) }}" method="post">
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

    <div style="margin-top:12px;">{{ $discounts->links() }}</div>
@endsection


