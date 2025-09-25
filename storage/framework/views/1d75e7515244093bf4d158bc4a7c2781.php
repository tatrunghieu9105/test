

<?php $__env->startSection('title','Quản lý mã giảm giá'); ?>
<?php $__env->startSection('page_title','Quản lý mã giảm giá'); ?>

<?php $__env->startSection('content'); ?>
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="<?php echo e(route('admin.discounts.create')); ?>">+ Tạo mới</a>
        <a class="btn" href="<?php echo e(route('admin.discounts.index')); ?>">Chỉ đang hoạt động</a>
        <a class="btn" href="<?php echo e(route('admin.discounts.index', ['with_trashed' => 1])); ?>">Bao gồm đã xóa</a>
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
            <?php $__currentLoopData = $discounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($d->id); ?></td>
                    <td><?php echo e($d->code); ?></td>
                    <td><?php echo e($d->type); ?></td>
                    <td><?php echo e($d->type==='percent' ? $d->value.'%' : number_format($d->value,0,',','.') .' đ'); ?></td>
                    <td><?php echo e($d->start_date); ?> → <?php echo e($d->end_date); ?></td>
                    <td><?php echo e($d->deleted_at ? 'Đã xóa' : 'Hoạt động'); ?></td>
                    <td>
                        <?php if(!$d->deleted_at): ?>
                            <a class="btn" href="<?php echo e(route('admin.discounts.edit', $d)); ?>">Sửa</a>
                            <form class="inline" action="<?php echo e(route('admin.discounts.destroy', $d)); ?>" method="post">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn" onclick="return confirm('Xóa (mềm) mã này?')">Xóa</button>
                            </form>
                        <?php else: ?>
                            <form class="inline" action="<?php echo e(route('admin.discounts.restore', $d->id)); ?>" method="post">
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

    <div style="margin-top:12px;"><?php echo e($discounts->links()); ?></div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/discounts/index.blade.php ENDPATH**/ ?>