@extends('admin.layout')

@section('title', 'Quản lý tài khoản')
@section('page_title', 'Quản lý tài khoản')

@section('content')
  <style>
    .btn-sm {
      padding: 4px 8px;
      font-size: 12px;
    }
    .btn-danger {
      background-color: #ef4444;
      color: white;
    }
    .status-active { color: #10b981; }
    .status-inactive { color: #ef4444; }
  </style>

  <div class="card" style="margin-bottom:12px">
    <form class="filter" method="get" style="display:flex;gap:12px;flex-wrap:wrap">
      <div class="form-row">
        <label>Từ khóa</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Tên hoặc email">
      </div>
      <div class="form-row">
        <label>Vai trò</label>
        <select name="role">
          <option value="">-- Tất cả --</option>
          @foreach($roles as $r)
            <option value="{{ $r->name }}" @selected($role===$r->name)>{{ ucfirst($r->name) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit">Lọc</button>
        <a class="btn" href="{{ route('admin.users.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Xóa lọc</a>
      </div>
    </form>
  </div>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Họ tên</th>
          <th>Email</th>
          <th>Trạng thái</th>
          <th>Vai trò</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->fullname }}</td>
            <td>{{ $user->email }}</td>
            <td>
              <span class="status-{{ $user->is_active ? 'active' : 'inactive' }}">
                {{ $user->is_active ? 'Đang hoạt động' : 'Đã khóa' }}
              </span>
            </td>
            <td>{{ optional($user->role)->name ?? '—' }}</td>
            <td>
              <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <form method="post" action="{{ route('admin.users.updateRole', $user) }}" class="inline">
                  @csrf
                  @method('PUT')
                  <select name="role_id" style="min-width: 120px;">
                    @foreach($roles as $role)
                      <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>
                        {{ ucfirst($role->name) }}
                      </option>
                    @endforeach
                  </select>
                  <button class="btn btn-sm" type="submit">Cập nhật</button>
                </form>
                
                @if(optional($user->role)->name !== 'admin' && $user->id !== auth()->id())
                  <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_active" value="{{ $user->is_active ? '0' : '1' }}">
                    <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-danger' : '' }}">
                      {{ $user->is_active ? 'Khóa' : 'Mở khóa' }}
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center">Không có dữ liệu</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
    <div style="margin-top:12px">{{ $users->links() }}</div>
  @endif
@endsection
