

<?php $__env->startSection('title','Tạo suất chiếu'); ?>
<?php $__env->startSection('page_title','Tạo suất chiếu'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        
        <?php if($errors->any()): ?>
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
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>


        <form id="showtimeForm" method="post" action="<?php echo e(route('admin.showtimes.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div class="form-row">
                    <label>Phim</label>
                    <select name="movie_id" required>
                        <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>"><?php echo e($m->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-row">
                    <label>Phòng</label>
                    <select name="room_id" required>
                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($r->id); ?>"><?php echo e($r->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

            
            <div id="timeError" class="text-danger" style="display:none; margin:8px 0"></div>

            <div class="form-actions">
                <button class="btn" type="submit">Lưu</button>
                <a class="btn" href="<?php echo e(route('admin.showtimes.index')); ?>" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
            const response = await fetch('<?php echo e(route("admin.showtimes.api")); ?>', {
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
            <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo e($m->id); ?>: <?php echo e((int)($m->duration ?? 0)); ?>,
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/showtimes/create.blade.php ENDPATH**/ ?>