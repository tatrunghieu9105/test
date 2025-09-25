<?php $__env->startSection('title','Sửa mã giảm giá'); ?>
<?php $__env->startSection('page_title','Sửa mã: '.$discount->code); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <form method="post" action="<?php echo e(route('admin.discounts.update', $discount)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="form-grid">
                <div class="form-row">
                    <label>Mã</label>
                    <input type="text" name="code" value="<?php echo e(old('code', $discount->code)); ?>" required>
                </div>
                <div class="form-row">
                    <label>Loại</label>
                    <select name="type">
                        <option value="percent" <?php if($discount->type==='percent'): echo 'selected'; endif; ?>>Phần trăm</option>
                        <option value="amount" <?php if($discount->type==='amount'): echo 'selected'; endif; ?>>Số tiền</option>
                    </select>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Giá trị</label>
                    <input type="number" step="0.01" name="value" value="<?php echo e(old('value', $discount->value)); ?>" required>
                    <div class="hint">Nếu là phần trăm, nhập 10 cho 10%.</div>
                </div>
                <div class="form-row">
                    <label>Giá trị đơn hàng tối thiểu</label>
                    <input type="number" step="1000" name="min_order_value" value="<?php echo e(old('min_order_value', $discount->min_order_value)); ?>" placeholder="0 (không yêu cầu)">
                    <div class="hint">Đơn hàng phải đạt giá trị tối thiểu này để áp dụng mã. Để 0 nếu không yêu cầu.</div>
                </div>
                <div class="form-row">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="start_date" value="<?php echo e(old('start_date', $discount->start_date)); ?>">
                </div>
                <div class="form-row">
                    <label>Ngày kết thúc</label>
                    <input type="date" name="end_date" value="<?php echo e(old('end_date', $discount->end_date)); ?>">
                </div>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Cập nhật</button>
                <a class="btn" href="<?php echo e(route('admin.discounts.index')); ?>" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/discounts/edit.blade.php ENDPATH**/ ?>