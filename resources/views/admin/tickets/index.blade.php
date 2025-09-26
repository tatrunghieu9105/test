@extends('admin.layout')

@section('title','Quản lý vé')
@section('page_title','Quản lý vé')

@section('content')

    <div class="card" style="margin-bottom:12px">
        <form method="get" action="" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap">
            <div class="form-row">
                <label>Trạng thái</label>
                <select name="status" onchange="this.form.submit()">
                    @php $status = request('status'); @endphp
                    <option value="" @selected($status==='')>Tất cả</option>
                    <option value="pending" @selected($status==='pending')>Chờ thanh toán</option>
                    <option value="paid_cash" @selected($status==='paid_cash')>Đã thanh toán tại quầy</option>
                    <option value="paid_online" @selected($status==='paid_online')>Đã thanh toán online</option>
                    <option value="used" @selected($status==='used')>Đã sử dụng</option>
                    <option value="cancelled" @selected($status==='cancelled')>Đã hủy</option>
                </select>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Phim</th>
                    <th>Suất</th>
                    <th>Ghế</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($tickets as $t)
                <tr>
                    <td><a href="{{ route('admin.tickets.show', $t) }}">#{{ $t->id }}</a></td>
                    <td>{{ $t->code }}</td>
                    <td>{{ optional(optional($t->showtime)->movie)->title }}</td>
                    <td>{{ optional($t->showtime)->start_time }}</td>
                    <td>{{ optional($t->seat)->code }}</td>
                    <td>{{ number_format($t->price,0,',','.') }} đ</td>
                    <td>
                        @php
                            $label = [
                                'pending' => 'Chờ thanh toán',
                                'paid_cash' => 'Đã thanh toán tại quầy',
                                'paid_online' => 'Đã thanh toán online',
                                'used' => 'Đã sử dụng',
                                'cancelled' => 'Đã hủy',
                            ][$t->status] ?? $t->status;
                        @endphp
                        {{ $label }}
                    </td>
                    <td>
                        @if($t->status !== 'cancelled')
                            <a class="btn" href="{{ route('admin.tickets.show', $t) }}">Xem chi tiết</a>
                        @else
                            <span style="color:#ef4444">Đã hủy</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;">{{ $tickets->links() }}</div>
@endsection


