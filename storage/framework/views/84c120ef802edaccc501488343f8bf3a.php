

<?php $__env->startSection('title','Thống kê theo phim'); ?>
<?php $__env->startSection('page_title','Thống kê theo phim'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card" style="margin-bottom:12px">
        <form method="get" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap">
            <div class="form-row">
                <label>Từ ngày</label>
                <input type="date" name="start" value="<?php echo e($start); ?>">
            </div>
            <div class="form-row">
                <label>Đến ngày</label>
                <input type="date" name="end" value="<?php echo e($end); ?>">
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Lọc</button>
                <a class="btn" href="<?php echo e(route('admin.stats.index')); ?>" style="background:transparent;border-color:var(--border);color:var(--text)">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Phim</th>
                    <th>Doanh thu</th>
                    <th>Số vé</th>
                    <th>Số suất</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $byMovie; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($title); ?></td>
                    <td><?php echo e(number_format($data['revenue'],0,',','.')); ?> đ</td>
                    <td><?php echo e($data['tickets']); ?></td>
                    <td><?php echo e($data['showtimes']); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/stats/index.blade.php ENDPATH**/ ?>