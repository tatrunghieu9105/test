

<?php $__env->startSection('title', 'Đơn hàng của tôi'); ?>

<?php $__env->startSection('content'); ?>
<h1 style="margin:0 0 12px 0">Đơn hàng của tôi</h1>

<div class="card">
  <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
      $movie = optional(optional($t->showtime)->movie);
      $room = optional(optional($t->showtime)->room);
      $time = optional($t->showtime)->start_time;
      $badge = match($t->status){
        'pending_cash' => 'Chưa thanh toán (quầy)',
        'pending_online' => 'Chưa thanh toán (online)',
        'paid_cash' => 'Đã thanh toán (quầy)',
        'paid_online' => 'Đã thanh toán (online)',
        'used' => 'Đã sử dụng',
        'cancelled' => 'Đã hủy',
        default => $t->status,
      };
    ?>
    <div class="card" style="margin-bottom:8px">
      <div class="row" style="justify-content:space-between">
        <div>
          <div><strong>#<?php echo e($t->code); ?></strong> • <?php echo e($movie->title ?? '—'); ?></div>
          <div class="muted" style="font-size:13px">Phòng <?php echo e($room->name ?? '—'); ?> • <?php echo e($time); ?></div>
          <div class="muted" style="font-size:13px">Ghế: <?php echo e(optional($t->seat)->code); ?></div>
        </div>
        <div style="text-align:right">
          <div><strong><?php echo e(number_format($t->price,0,',','.')); ?> đ</strong></div>
          <span class="badge" style="margin-top:4px"><?php echo e($badge); ?></span>
          <div style="margin-top:6px">
            <?php if($t->status !== 'cancelled'): ?>
              <a class="btn" href="<?php echo e(route('me.tickets.show', $t)); ?>">Xem vé</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="muted">Bạn chưa có vé nào.</div>
  <?php endif; ?>
</div>

<div style="margin-top:12px;"><?php echo e($tickets->links()); ?></div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('client.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/client/orders.blade.php ENDPATH**/ ?>