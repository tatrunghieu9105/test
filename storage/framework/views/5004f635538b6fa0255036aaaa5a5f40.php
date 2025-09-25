

<?php $__env->startSection('title','Quản lý suất chiếu'); ?>
<?php $__env->startSection('page_title','Quản lý suất chiếu'); ?>

<?php $__env->startSection('content'); ?>
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="<?php echo e(route('admin.showtimes.create')); ?>">+ Tạo mới</a>
        <a class="btn <?php echo e(request('with_trashed') ? 'active' : ''); ?>" href="<?php echo e(route('admin.showtimes.index', ['with_trashed' => 1] + request()->except('with_trashed'))); ?>">Suất đã xóa</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phim</th>
                    <th>Phòng</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $showtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($st->id); ?></td>
                    <td><?php echo e(optional($st->movie)->title); ?></td>
                    <td><?php echo e(optional($st->room)->name); ?></td>
                    <td><?php echo e($st->start_time); ?></td>
                    <td><?php echo e($st->end_time); ?></td>
                    <td><?php echo e(number_format($st->price, 0, ',', '.')); ?> đ</td>
                    <td>
                        <?php if(!$st->deleted_at): ?>
                            <a class="btn" href="<?php echo e(route('admin.showtimes.edit', $st)); ?>">Sửa</a>
                            <form class="inline" action="<?php echo e(route('admin.showtimes.destroy', $st)); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn" onclick="return confirm('Xóa (mềm) suất chiếu này?')">Xóa</button>
                            </form>
                        <?php else: ?>
                            <form class="inline" action="<?php echo e(route('admin.showtimes.restore', $st->id)); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <button class="btn">Khôi phục</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;"><?php echo e($showtimes->links()); ?></div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/showtimes/index.blade.php ENDPATH**/ ?>