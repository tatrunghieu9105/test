

<?php $__env->startSection('title','Quản lý vé'); ?>
<?php $__env->startSection('page_title','Quản lý vé'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="flash success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="flash error"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="card" style="margin-bottom:12px">
        <form method="get" action="" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap">
            <div class="form-row">
                <label>Trạng thái</label>
                <select name="status" onchange="this.form.submit()">
                    <?php $status = request('status'); ?>
                    <option value="" <?php if($status===''): echo 'selected'; endif; ?>>Tất cả</option>
                    <option value="pending" <?php if($status==='pending'): echo 'selected'; endif; ?>>Chờ thanh toán</option>
                    <option value="paid_cash" <?php if($status==='paid_cash'): echo 'selected'; endif; ?>>Đã thanh toán tại quầy</option>
                    <option value="paid_online" <?php if($status==='paid_online'): echo 'selected'; endif; ?>>Đã thanh toán online</option>
                    <option value="used" <?php if($status==='used'): echo 'selected'; endif; ?>>Đã sử dụng</option>
                    <option value="cancelled" <?php if($status==='cancelled'): echo 'selected'; endif; ?>>Đã hủy</option>
                </select>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Phim</th>
                    <th>Suất</th>
                    <th>Ghế</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><a href="<?php echo e(route('admin.tickets.show', $t)); ?>">#<?php echo e($t->id); ?></a></td>
                    <td><?php echo e($t->code); ?></td>
                    <td><?php echo e(optional(optional($t->showtime)->movie)->title); ?></td>
                    <td><?php echo e(optional($t->showtime)->start_time); ?></td>
                    <td><?php echo e(optional($t->seat)->code); ?></td>
                    <td><?php echo e(number_format($t->price,0,',','.')); ?> đ</td>
                    <td>
                        <?php
                            $label = [
                                'pending' => 'Chờ thanh toán',
                                'paid_cash' => 'Đã thanh toán tại quầy',
                                'paid_online' => 'Đã thanh toán online',
                                'used' => 'Đã sử dụng',
                                'cancelled' => 'Đã hủy',
                            ][$t->status] ?? $t->status;
                        ?>
                        <?php echo e($label); ?>

                    </td>
                    <td>
                        <?php if($t->status !== 'cancelled'): ?>
                            <a class="btn" href="<?php echo e(route('admin.tickets.show', $t)); ?>">Xem chi tiết</a>
                        <?php else: ?>
                            <span style="color:#ef4444">Đã hủy</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;"><?php echo e($tickets->links()); ?></div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/tickets/index.blade.php ENDPATH**/ ?>