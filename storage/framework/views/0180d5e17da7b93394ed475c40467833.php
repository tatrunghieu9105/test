<?php $__env->startSection('title','Quản lý phim'); ?>
<?php $__env->startSection('page_title','Quản lý phim'); ?>

<?php $__env->startSection('content'); ?>
    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px">
        <a class="btn" href="<?php echo e(route('admin.movies.create')); ?>">+ Tạo phim</a>
        <a class="btn" href="<?php echo e(route('admin.movies.index')); ?>">Chỉ đang hoạt động</a>
        <a class="btn" href="<?php echo e(route('admin.movies.index', ['with_trashed'=>1])); ?>">Bao gồm đã xóa</a>
        <a class="btn" href="<?php echo e(route('admin.dashboard')); ?>" style="margin-left:auto;">← Dashboard</a>
    </div>


    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Thời lượng</th>
                    <th>Ngày phát hành</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($m->id); ?></td>
                    <td><?php echo e($m->title); ?></td>
                    <td><?php echo e($m->category->name ?? '—'); ?></td>
                    <td><?php echo e($m->duration); ?>'</td>
                    <td><?php echo e($m->release_date); ?></td>
                    <td><?php echo e($m->deleted_at ? 'Đã xóa' : 'Hoạt động'); ?></td>
                    <td>
                        <?php if(!$m->deleted_at): ?>
                            <a class="btn" href="<?php echo e(route('admin.movies.edit', $m)); ?>">Sửa</a>
                            <form class="inline" action="<?php echo e(route('admin.movies.destroy', $m)); ?>" method="post">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn" onclick="return confirm('Xóa (mềm) phim này?')">Xóa</button>
                            </form>
                        <?php else: ?>
                            <form class="inline" action="<?php echo e(route('admin.movies.restore', $m->id)); ?>" method="post">
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

    <div style="margin-top:12px"><?php echo e($movies->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/movies/index.blade.php ENDPATH**/ ?>