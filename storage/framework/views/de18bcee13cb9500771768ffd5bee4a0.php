<?php $__env->startSection('title','Chi tiết vé'); ?>
<?php $__env->startSection('page_title'); ?>
    <div class="flex justify-between items-center">
        <div>Chi tiết vé #<?php echo e($ticket->id); ?></div>
<?php $__env->stopSection(); ?>

<?php
  $movie = optional(optional($ticket->showtime)->movie);
  $room  = optional(optional($ticket->showtime)->room);
  $start = optional($ticket->showtime?->start_time)?->format('d/m/Y H:i');
  $end   = optional($ticket->showtime?->end_time)?->format('H:i');
  $badge = match($ticket->status){
    'pending_cash' => 'Chưa thanh toán (quầy)',
    'pending_online' => 'Chưa thanh toán (online)',
    'paid_cash' => 'Đã thanh toán (quầy)',
    'paid_online' => 'Đã thanh toán (online)',
    'used' => 'Đã sử dụng',
    'cancelled' => 'Đã hủy',
    default => $ticket->status,
  };
?>

<?php $__env->startSection('content'); ?>
  <div class="card" style="max-width:880px">
    <div class="row" style="justify-content:space-between; align-items:flex-start">
      <div>
        <div class="muted" style="font-size:13px">Mã vé</div>
        <div style="margin-bottom:8px"><strong><?php echo e($ticket->code); ?></strong></div>
        <div class="muted" style="font-size:13px">Khách</div>
        <div style="margin-bottom:8px"><?php echo e(optional($ticket->user)->fullname); ?> (<?php echo e(optional($ticket->user)->email); ?>)</div>
        <div class="muted" style="font-size:13px">Phim</div>
        <div style="margin-bottom:8px"><?php echo e($movie->title ?? '—'); ?></div>
        <div class="muted" style="font-size:13px">Phòng • Suất</div>
        <div style="margin-bottom:8px"><?php echo e($room->name ?? '—'); ?> • <?php echo e($start); ?> - <?php echo e($end); ?></div>
        <div class="muted" style="font-size:13px">Ghế</div>
        <div style="margin-bottom:8px"><?php echo e(optional($ticket->seat)->code); ?></div>
      </div>
      <div style="text-align:right">
        <div class="muted" style="font-size:13px">Giá</div>
        <div><strong><?php echo e(number_format($ticket->price,0,',','.')); ?> đ</strong></div>
        <div style="margin-top:6px"><span class="badge"><?php echo e($badge); ?></span></div>
        <?php if($ticket->used_at): ?>
          <div class="muted" style="margin-top:6px;font-size:12px">Used at: <?php echo e(\Carbon\Carbon::parse($ticket->used_at)->format('d/m/Y H:i')); ?></div>
        <?php endif; ?>
        <?php if($ticket->discount): ?>
          <div class="muted" style="margin-top:6px;font-size:12px">Mã giảm: <?php echo e($ticket->discount->code); ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="row" style="gap:8px; margin-top:12px">
    <?php if($ticket->status==='pending_cash'): ?>
        <form action="<?php echo e(route('admin.tickets.markPaid', $ticket)); ?>" method="post" class="inline"><?php echo csrf_field(); ?><button class="btn">Xác nhận đã thanh toán</button></form>
        <form action="<?php echo e(route('admin.tickets.cancel', $ticket)); ?>" method="post" class="inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn" onclick="return confirm('Hủy vé?')">Hủy</button></form>
    <?php elseif($ticket->status==='paid_cash'): ?>
        <form action="<?php echo e(route('admin.tickets.markUsed', $ticket)); ?>" method="post" class="inline"><?php echo csrf_field(); ?><button class="btn">Check-in</button></form>
    <?php elseif($ticket->status==='pending_online'): ?>
        <form action="<?php echo e(route('admin.tickets.cancel', $ticket)); ?>" method="post" class="inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn" onclick="return confirm('Hủy vé?')">Hủy</button></form>
    <?php elseif($ticket->status==='paid_online'): ?>
        <form action="<?php echo e(route('admin.tickets.markUsed', $ticket)); ?>" method="post" class="inline"><?php echo csrf_field(); ?><button class="btn">Check-in</button></form>
    <?php endif; ?>
    <a href="<?php echo e(route('admin.tickets.index')); ?>" class="btn">Quay lại</a>
  </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/tickets/show.blade.php ENDPATH**/ ?>