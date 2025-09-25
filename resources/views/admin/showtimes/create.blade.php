@extends('admin.layout')

@section('title','Tạo suất chiếu')
@section('page_title','Tạo suất chiếu')

@section('content')
    <div class="card">
        {{-- Hiển thị lỗi validation --}}
        @if($errors->any())
    <div class="alert alert-danger" style="
        border:1px solid #f5c2c7;
        background:#f8d7da;
        color:#842029;
        border-radius:8px;
        padding:12px 16px;
        margin-bottom:16px;
        font-size:14px;
    ">
        <strong style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
            ⚠️ Có lỗi xảy ra:
        </strong>
        <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


        <form id="showtimeForm" method="post" action="{{ route('admin.showtimes.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-row">
                    <label>Phim</label>
                    <select name="movie_id" required>
                        @foreach ($movies as $m)
                            <option value="{{ $m->id }}">{{ $m->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Phòng</label>
                    <select name="room_id" required>
                        @foreach ($rooms as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Bắt đầu</label>
                    <input type="datetime-local" name="start_time" required>
                </div>
                <div class="form-row">
                    <label>Kết thúc</label>
                    <input type="datetime-local" name="end_time" required>
                </div>
            </div>

            <div class="form-row">
                <label>Giá</label>
                <input type="number" name="price" min="0" step="1000" value="90000" required>
            </div>

            {{-- Nơi hiển thị lỗi khi check API --}}
            <div id="timeError" class="text-danger" style="display:none; margin:8px 0"></div>

            <div class="form-actions">
                <button class="btn" type="submit">Lưu</button>
                <a class="btn" href="{{ route('admin.showtimes.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('showtimeForm');
    const roomSelect = document.querySelector('select[name="room_id"]');
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    const movieSelect = document.querySelector('select[name="movie_id"]');
    const errorDiv = document.getElementById('timeError');

    // Lắng nghe sự kiện submit form
    form.addEventListener('submit', async function(e) {
        e.preventDefault(); // Ngăn form submit mặc định

        // Kiểm tra xung đột lịch chiếu
        const checkResponse = await checkTimeConflict();
        
        if (checkResponse.overlap) {
            // Hiển thị thông báo lỗi
            errorDiv.textContent = checkResponse.message;
            errorDiv.style.display = 'block';
            return false;
        }

        // Nếu không có xung đột, submit form
        form.submit();
    });

    // Hàm kiểm tra xung đột thời gian
    async function checkTimeConflict() {
        if (!roomSelect.value || !startTimeInput.value || !endTimeInput.value) {
            return { overlap: false };
        }

        try {
            const response = await fetch('{{ route("admin.showtimes.api") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    room_id: roomSelect.value,
                    start_time: startTimeInput.value,
                    end_time: endTimeInput.value,
                    exclude_id: ''
                })
            });

            if (!response.ok) {
                throw new Error('Lỗi khi kiểm tra lịch chiếu');
            }

            return await response.json();
        } catch (error) {
            console.error('Lỗi:', error);
            return { overlap: false, message: 'Có lỗi xảy ra khi kiểm tra lịch chiếu' };
        }
    }

    // Tự động tính thời gian kết thúc dựa trên thời lượng phim
    if (movieSelect) {
        const movieDurations = {
            @foreach($movies as $m)
                {{ $m->id }}: {{ (int)($m->duration ?? 0) }},
            @endforeach
        };

        function calculateEndTime() {
            if (!startTimeInput.value || !movieSelect.value) return;
            
            const movieId = movieSelect.value;
            const duration = movieDurations[movieId] || 0;
            if (!duration) return;

            const startTime = new Date(startTimeInput.value);
            startTime.setMinutes(startTime.getMinutes() + duration + 15); // Thêm 15 phút dọn dẹp
            
            const pad = n => String(n).padStart(2, '0');
            const formattedTime = `${startTime.getFullYear()}-${pad(startTime.getMonth() + 1)}-${pad(startTime.getDate())}T${pad(startTime.getHours())}:${pad(startTime.getMinutes())}`;
            
            endTimeInput.value = formattedTime;
        }

        movieSelect.addEventListener('change', calculateEndTime);
        startTimeInput.addEventListener('change', calculateEndTime);
    }

    // Kiểm tra xung đột khi thay đổi thời gian hoặc phòng
    [roomSelect, startTimeInput, endTimeInput].forEach(element => {
        element?.addEventListener('change', async function() {
            if (roomSelect.value && startTimeInput.value && endTimeInput.value) {
                const result = await checkTimeConflict();
                if (result.overlap) {
                    errorDiv.textContent = result.message;
                    errorDiv.style.display = 'block';
                } else {
                    errorDiv.style.display = 'none';
                }
            }
        });
    });
});
</script>
@endsection
