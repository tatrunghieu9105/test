

<?php $__env->startSection('title', 'Chọn phương thức thanh toán'); ?>

<?php $__env->startSection('content'); ?>
<h1 style="margin:0 0 12px 0">Chọn phương thức thanh toán</h1>
<?php
    $showtime = \App\Models\Showtime::with(['movie','room'])->find($showtime_id);
    $combo = $combo_id ? \App\Models\Combo::find($combo_id) : null;
    $discount = $discount_code ? \App\Models\DiscountCode::where('code', $discount_code)->first() : null;
    $numSeats = count($seat_ids);
    $basePrice = $showtime ? $showtime->price : 0;
    $subtotal = $basePrice * $numSeats;
    $discountAmount = 0;
    if ($discount) {
        $discountAmount = $discount->type === 'percent' ? round($subtotal * $discount->value / 100, 2) : $discount->value;
        if ($discountAmount > $subtotal) $discountAmount = $subtotal;
    }
    $comboAmount = $combo?->price ?? 0;
    $total = max(0, $subtotal - $discountAmount) + $comboAmount;
?>

<div class="row" style="gap:16px; align-items:flex-start">
  <form class="card" method="post" action="<?php echo e(route('payments.create')); ?>" style="flex:1">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="showtime_id" value="<?php echo e($showtime_id); ?>">
      <?php $__currentLoopData = $seat_ids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <input type="hidden" name="seat_ids[]" value="<?php echo e($id); ?>">
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if($discount_code): ?>
          <input type="hidden" name="discount_code" value="<?php echo e($discount_code); ?>">
      <?php endif; ?>
      <?php if($combo_id): ?>
          <input type="hidden" name="combo_id" value="<?php echo e($combo_id); ?>">
      <?php endif; ?>

      <h3 style="margin-top:0">Phương thức</h3>
      <label class="row" style="gap:8px"><input type="radio" name="gateway" value="cash" checked> Thanh toán tiền mặt tại quầy</label>
      <label class="row" style="gap:8px"><input type="radio" name="gateway" value="momo"> Thanh toán qua MoMo</label>
      <label class="row" style="gap:8px"><input type="radio" name="gateway" value="vnpay"> Thanh toán qua VNPay</label>

      <div class="row" style="margin-top:12px">
        <button type="submit" class="btn">Xác nhận thanh toán</button>
      </div>
  </form>

  <div class="card" style="min-width:280px">
      <h3 style="margin-top:0">Tóm tắt đơn</h3>
      <div class="muted" style="font-size:14px">Phim: <?php echo e(optional($showtime->movie)->title); ?></div>
      <div class="muted" style="font-size:14px">Thời gian: <?php echo e($showtime && $showtime->start_time ? \Carbon\Carbon::parse($showtime->start_time)->format('d/m/Y H:i') : '—'); ?></div>
      <div class="muted" style="font-size:14px">Phòng: <?php echo e(optional($showtime->room)->name); ?></div>
      <div class="muted" style="font-size:14px">Số ghế: <?php echo e($numSeats); ?></div>
      <div style="border-top:1px solid var(--border); margin:10px 0"></div>
      <div class="row" style="justify-content:space-between"><span>Tạm tính</span><strong><?php echo e(number_format($subtotal, 0, ',', '.')); ?> đ</strong></div>
      <div class="row" style="justify-content:space-between"><span>Giảm giá</span><strong>-<?php echo e(number_format($discountAmount, 0, ',', '.')); ?> đ</strong></div>
      <div class="row" style="justify-content:space-between"><span>Combo</span><strong><?php echo e(number_format($comboAmount, 0, ',', '.')); ?> đ</strong></div>
      <div class="row" style="justify-content:space-between"><span>Tổng</span><strong style="color:#22c55e"><?php echo e(number_format($total, 0, ',', '.')); ?> đ</strong></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/client/bookings/payment_method.blade.php ENDPATH**/ ?>