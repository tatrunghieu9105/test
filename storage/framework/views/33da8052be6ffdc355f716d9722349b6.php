<?php $__env->startSection('title','Tạo mã giảm giá'); ?>
<?php $__env->startSection('page_title','Tạo mã giảm giá'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <form method="post" action="<?php echo e(route('admin.discounts.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div class="form-row">
                    <label>Mã</label>
                    <input type="text" name="code" placeholder="SALE10" required>
                </div>
                <div class="form-row">
                    <label>Loại</label>
                    <select name="type">
                        <option value="percent">Phần trăm</option>
                        <option value="amount">Số tiền</option>
                    </select>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Giá trị</label>
                    <input type="number" step="0.01" name="value" placeholder="Ví dụ: 10 hoặc 50000" required>
                    <div class="hint">Nếu là phần trăm, nhập 10 cho 10%.</div>
                </div>
                <div class="form-row">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="start_date">
                </div>
                <div class="form-row">
                    <label>Ngày kết thúc</label>
                    <input type="date" name="end_date">
                </div>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Lưu</button>
                <a class="btn" href="<?php echo e(route('admin.discounts.index')); ?>" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/discounts/create.blade.php ENDPATH**/ ?>