@extends('admin.layout')

@section('title','Nhật ký thao tác')
@section('page_title','Nhật ký thao tác')

@section('content')
<div class="card" style="margin-bottom:12px">
<form method="get" style="display:flex;gap:12px;flex-wrap:wrap">
    <div style="display:flex;flex-direction:column">
        <div>
            <label>Người thao tác</label>
            <select name="admin_id">
                <option value="">-- Tất cả --</option>
                @foreach($admins as $a)
                    <option value="{{ $a->id }}" @selected(request('admin_id') == $a->id)>{{ $a->fullname }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;flex-direction:column">
            <label>Hành động</label>
            @php
                $actionVN = [
                    'created' => 'Tạo',
                    'updated' => 'Cập nhật',
                    'deleted' => 'Xóa',
                    'restored' => 'Khôi phục',
                    'create' => 'Tạo',
                    'update' => 'Cập nhật',
                    'delete' => 'Xóa',
                    'restore' => 'Khôi phục',
                ];
            @endphp
            <select name="action">
                <option value="">-- Tất cả --</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" @selected(request('action') == $act)>{{ $actionVN[$act] ?? $act }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;flex-direction:column">
            <label>Bảng</label>
            @php
                $tableVN = [
                    'categories' => 'Thể loại',
                    'actors' => 'Diễn viên',
                    'movies' => 'Phim',
                    'rooms' => 'Phòng chiếu',
                    'seats' => 'Ghế',
                    'showtimes' => 'Suất chiếu',
                    'combos' => 'Combo',
                    'discount_codes' => 'Mã giảm giá',
                    'tickets' => 'Vé',
                    'users' => 'Người dùng',
                ];
            @endphp
            <select name="table_name">
                <option value="">-- Tất cả --</option>
                @foreach($tables as $t)
                    <option value="{{ $t }}" @selected(request('table_name') == $t)>{{ $tableVN[$t] ?? $t }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;flex-direction:column">
            <label>Từ ngày</label>
            <input type="date" name="from" value="{{ request('from') }}">
        </div>
        <div style="display:flex;flex-direction:column">
            <label>Đến ngày</label>
            <input type="date" name="to" value="{{ request('to') }}">
        </div>
        <div class="form-actions">
            <button class="btn" type="submit">Lọc</button>
            <a class="btn" href="{{ route('admin.logs.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Xóa lọc</a>
        </div>
    </div>
</form>
</div>

<div class="card">
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Thời gian</th>
            <th>Người thao tác</th>
            <th>Hành động</th>
            <th>Bảng</th>
            <th>Bản ghi</th>
            <th>Nội dung</th>
        </tr>
    </thead>
    <tbody>
        @php
            $hiddenFields = ['password','remember_token','updated_at','created_at','deleted_at','email_verified_at'];
            $formatVal = function($val){
                if (is_bool($val)) return $val ? 'Có' : 'Không';
                if ($val === null || $val === '') return '—';
                if (is_array($val)) return json_encode($val, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                return (string) $val;
            };
            // Nhãn tiếng Việt cho các trường phổ biến
            $fieldVNCommon = [
                'name' => 'Tên',
                'title' => 'Tiêu đề',
                'description' => 'Mô tả',
                'price' => 'Giá',
                'type' => 'Loại',
                'value' => 'Giá trị',
                'code' => 'Mã',
                'rows' => 'Số hàng',
                'cols' => 'Số cột',
                'layout' => 'Sơ đồ',
                'total_seats' => 'Tổng ghế',
                'duration' => 'Thời lượng (phút)',
                'release_date' => 'Ngày phát hành',
                'start_time' => 'Bắt đầu',
                'end_time' => 'Kết thúc',
                'price' => 'Giá',
                'status' => 'Trạng thái',
                'used_at' => 'Thời gian sử dụng',
                'points' => 'Điểm',
            ];
            // Bản đồ theo bảng (ưu tiên)
            $fieldVNByTable = [
                'categories' => [ 'name' => 'Tên thể loại' ],
                'actors' => [ 'name' => 'Tên diễn viên', 'bio' => 'Tiểu sử', 'birth_date' => 'Ngày sinh' ],
                'movies' => [ 'title' => 'Tên phim', 'poster_url' => 'Ảnh poster', 'trailer_url' => 'Link trailer' ],
                'rooms' => [ 'name' => 'Tên phòng', 'vip_last_rows' => 'Số hàng VIP cuối' ],
                'seats' => [ 'code' => 'Mã ghế', 'type' => 'Loại ghế' ],
                'showtimes' => [ 'price' => 'Giá vé' ],
                'combos' => [ 'name' => 'Tên combo' ],
                'discount_codes' => [ 'code' => 'Mã giảm', 'type' => 'Kiểu giảm', 'value' => 'Giá trị' ],
                'tickets' => [ 'status' => 'Trạng thái', 'used_at' => 'Thời gian sử dụng' ],
            ];
            $fieldLabel = function($table, $field) use ($fieldVNByTable, $fieldVNCommon) {
                return $fieldVNByTable[$table][$field] ?? $fieldVNCommon[$field] ?? $field;
            };
        @endphp
        @forelse($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ optional($log->created_at)->format('d/m/Y H:i:s') }}</td>
                <td>{{ $log->admin->fullname ?? 'N/A' }}</td>
                <td>{{ $actionVN[$log->action] ?? $log->action }}</td>
                <td>{{ $tableVN[$log->table_name] ?? $log->table_name }}</td>
                <td>{{ $log->record_id }}</td>
                <td style="max-width:600px; white-space:normal">
                    @php($desc = $log->description_array)
                    @if(!empty($desc['message']))
                        <strong>{{ $desc['message'] }}</strong>
                        @if($log->table_name === 'tickets')
                            <span>(Mã vé: {{ $log->record_id }})</span>
                        @endif
                    @elseif(isset($desc['changed']) && is_array($desc['changed']) && count($desc['changed']))
                        <ul style="margin:0; padding-left:18px">
                            @foreach($desc['changed'] as $field => $pair)
                                @continue(in_array($field, $hiddenFields, true))
                                <li><strong>{{ $fieldLabel($log->table_name, $field) }}</strong>: {{ $formatVal($pair['from'] ?? null) }} → <strong>{{ $formatVal($pair['to'] ?? null) }}</strong></li>
                            @endforeach
                        </ul>
                    @elseif(isset($desc['attributes']) && is_array($desc['attributes']))
                        <ul style="margin:0; padding-left:18px">
                            @foreach($desc['attributes'] as $k => $v)
                                @continue(in_array($k, array_merge($hiddenFields, ['id']), true))
                                <li><strong>{{ $fieldLabel($log->table_name, $k) }}</strong>: {{ $formatVal($v) }}</li>
                            @endforeach
                        </ul>
                    @elseif(isset($desc['label']))
                        <span>Bản ghi: {{ $desc['label'] ?? 'N/A' }} (ID: {{ $desc['id'] ?? $log->record_id }})</span>
                    @else
                        <em>Không có thay đổi hoặc dữ liệu cũ.</em>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center">Không có dữ liệu</td></tr>
        @endforelse
    </tbody>
</table>
</div>

<div style="margin-top:12px">{{ $logs->links() }}</div>
@endsection
