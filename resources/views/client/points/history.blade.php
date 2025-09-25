@extends('client.layout')

@section('title', 'Lịch sử điểm thưởng')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">Lịch sử điểm thưởng</h4>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="text-muted small">Điểm hiện tại</div>
                    <div class="h4 mb-0">{{ number_format(auth()->user()->points) }} điểm</div>
                </div>
                <div>
                    <div class="text-muted small">Hạng thành viên</div>
                    <div class="h4 mb-0">
                        <span class="badge bg-primary">{{ auth()->user()->membership_level }}</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('me.orders') }}" class="btn btn-outline-primary">
                <i class="fas fa-ticket-alt me-2"></i>Đơn hàng của tôi
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Mô tả</th>
                        <th>Điểm</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                    <tr>
                        <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            {{ $history->description }}
                            @if($history->ticket)
                            <div class="text-muted small">
                                Mã vé: {{ $history->ticket->code }}
                            </div>
                            @endif
                        </td>
                        <td class="{{ $history->action === 'earned' ? 'text-success' : 'text-danger' }}">
                            {{ $history->action === 'earned' ? '+' : '-' }}{{ number_format($history->points) }}
                        </td>
                        <td>
                            @if($history->action === 'earned')
                                <span class="badge bg-success">Đã nhận</span>
                            @elseif($history->action === 'used')
                                <span class="badge bg-warning text-dark">Đã sử dụng</span>
                            @else
                                <span class="badge bg-secondary">Hết hạn</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="text-muted">Chưa có lịch sử điểm thưởng</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $histories->links() }}
        </div>
        
        <div class="alert alert-info mt-4">
            <h6><i class="fas fa-info-circle me-2"></i>Quy đổi điểm thưởng</h6>
            <ul class="mb-0">
                <li>1 điểm = 1.000 VND (áp dụng tối đa 50% giá trị đơn hàng)</li>
                <li>Điểm có thời hạn 1 năm kể từ ngày phát sinh</li>
                <li>Điểm sẽ tự động được sử dụng cho đơn hàng tiếp theo</li>
            </ul>
        </div>
    </div>
</div>
@endsection
