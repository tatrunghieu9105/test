@extends('admin.layout')

@section('title', 'Báo cáo thành viên')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Báo cáo thành viên</h4>
        
        <form method="get" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Lọc</button>
            </div>
        </form>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Phân bố hạng thành viên</h5>
                        <canvas id="tierChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu theo hạng ({{ date('d/m/Y', strtotime($dateFrom)) }} - {{ date('d/m/Y', strtotime($dateTo)) }})</h5>
                        <canvas id="revenueChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h5>Top 10 thành viên tích cực</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Hạng</th>
                            <th>Điểm</th>
                            <th>Số vé đã đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topUsers as $i => $user)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-primary">{{ $user->membership_level }}</span></td>
                            <td>{{ number_format($user->points) }}</td>
                            <td>{{ $user->tickets_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Phân bố hạng thành viên
new Chart(document.getElementById('tierChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($usersByTier)) !!},
        datasets: [{
            data: {!! json_encode(array_values($usersByTier)) !!},
            backgroundColor: ['#6c757d', '#0dcaf0', '#ffc107', '#0d6efd']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) label += ': ';
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const value = context.raw || 0;
                        const percentage = Math.round((value / total) * 100);
                        return label + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Doanh thu theo hạng
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($revenueByTier)) !!},
        datasets: [{
            label: 'Doanh thu (VND)',
            data: {!! json_encode(array_values($revenueByTier)) !!},
            backgroundColor: 'rgba(13, 110, 253, 0.5)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + ' đ';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y.toLocaleString('vi-VN') + ' đ';
                    }
                }
            }
        }
    }
});
</script>
@endpush
