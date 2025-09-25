

<?php $__env->startSection('title', 'Vé '.$ticket->code); ?>

<?php $__env->startSection('content'); ?>
<h1 style="margin:0 0 12px 0">Vé #<?php echo e($ticket->code); ?></h1>

<?php
  $movie = optional(optional($ticket->showtime)->movie);
  $room  = optional(optional($ticket->showtime)->room);
  $start = optional($ticket->showtime)->start_time;
  $end   = optional($ticket->showtime)->end_time;
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

<div class="card" style="max-width:880px">
  <div class="row" style="gap:16px; align-items:flex-start">
    <div style="flex:1">
      <div class="row" style="justify-content:space-between">
        <div>
          <div class="muted" style="font-size:13px">Phim</div>
          <div style="margin-bottom:8px"><strong><?php echo e($movie->title ?? '—'); ?></strong></div>
        </div>
        <div style="text-align:right">
          <span class="badge"><?php echo e($badge); ?></span>
        </div>
      </div>
      <div class="row" style="gap:20px; flex-wrap:wrap">
        <div><span class="muted">Phòng</span> • <strong><?php echo e($room->name ?? '—'); ?></strong></div>
        <div><span class="muted">Suất</span> • <strong><?php echo e($start?->format('d/m/Y H:i')); ?> - <?php echo e($end?->format('H:i')); ?></strong></div>
        <div><span class="muted">Ghế</span> • <strong><?php echo e(optional($ticket->seat)->code); ?></strong></div>
        <div><span class="muted">Giá</span> • <strong><?php echo e(number_format($ticket->price,0,',','.')); ?> đ</strong></div>
      </div>
      <?php if($ticket->used_at): ?>
      <div class="muted" style="margin-top:6px;font-size:13px">Đã sử dụng: <?php echo e(\Carbon\Carbon::parse($ticket->used_at)->format('d/m/Y H:i')); ?></div>
      <?php endif; ?>
    </div>

    <div class="card" style="min-width:260px; text-align:center">
      <div class="muted">Mã vé / Quét tại quầy</div>
      <div style="font-family:ui-monospace, SFMono-Regular, Menlo, monospace; background:#0d152b; border:1px solid var(--border); border-radius:8px; padding:8px; margin:8px 0;word-break:break-all">
        <?php echo e($qrText); ?>

      </div>
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo e(urlencode($qrText)); ?>" alt="QR Code" style="border-radius:8px;border:1px solid var(--border)"/>
    </div>
  </div>
</div>

<div class="row" style="gap:10px; margin-top:12px">
  <?php if(in_array($ticket->status, ['pending_cash', 'pending_online'])): ?>
    <form method="POST" action="<?php echo e(route('me.tickets.cancel', $ticket)); ?>" onsubmit="return confirm('Bạn chắc chắn muốn hủy vé này?')">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-outline" style="border-color:#ef4444;color:#ef4444">Hủy vé</button>
    </form>
  <?php endif; ?>
  <a class="btn" href="<?php echo e(route('me.orders')); ?>">Quay lại đơn hàng</a>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('client.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/client/ticket_show.blade.php ENDPATH**/ ?>