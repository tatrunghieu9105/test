<?php $__env->startSection('title', $movie->title); ?>

<?php $__env->startSection('content'); ?>
<div class="row" style="align-items:flex-start">
  <div style="flex:0 0 280px">
    <img class="poster" src="<?php echo e($movie->poster_url ?: 'https://picsum.photos/600/900?blur=3'); ?>" alt="<?php echo e($movie->title); ?>">
  </div>
  <div style="flex:1">
    <h1 style="margin:0 0 6px 0"><?php echo e($movie->title); ?></h1>
    <div class="row" style="gap:8px; margin-bottom:6px">
      <span class="badge"><?php echo e(optional($movie->category)->name ?? '—'); ?></span>
      <?php if($movie->release_date): ?>
        <span class="badge">Phát hành: <?php echo e($movie->release_date); ?></span>
      <?php endif; ?>
      <?php if($movie->duration): ?>
        <span class="badge"><?php echo e($movie->duration); ?> phút</span>
      <?php endif; ?>
    </div>
    <?php if($movie->trailer_url): ?>
      <p><a class="btn btn-outline" href="<?php echo e($movie->trailer_url); ?>" target="_blank">Xem trailer</a></p>
    <?php endif; ?>
    <p class="muted" style="white-space:pre-line"><?php echo e($movie->description); ?></p>

    <div class="row" style="gap:6px; margin:10px 0 18px 0; flex-wrap:wrap">
      <?php $__empty_1 = true; $__currentLoopData = $movie->actors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <span class="badge"><?php echo e($actor->name); ?></span>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <span class="muted">Chưa có diễn viên</span>
      <?php endif; ?>
    </div>

    <h3 style="margin:12px 0 8px 0">Suất chiếu sắp tới</h3>
    <?php
      $grouped = collect($movie->showtimes)->groupBy(fn($st)=>\Carbon\Carbon::parse($st->start_time)->format('d/m/Y'));
    ?>
    <?php $__empty_1 = true; $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card" style="margin-bottom:10px; padding:10px">
        <div class="row" style="justify-content:space-between; margin-bottom:8px">
          <strong><?php echo e($date); ?></strong>
          <span class="muted"><?php echo e(count($items)); ?> suất</span>
        </div>
        <div class="row" style="gap:8px; flex-wrap:wrap">
          <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card" style="padding:8px; border-radius:10px; min-width:140px">
              <div class="muted" style="font-size:12px">Phòng <?php echo e(optional($st->room)->name); ?></div>
              <div style="font-size:16px; font-weight:600">
                <?php echo e(\Carbon\Carbon::parse($st->start_time)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($st->end_time)->format('H:i')); ?>

              </div>
              <div class="muted" style="font-size:12px">
                <?php echo e(number_format($st->price,0,',','.')); ?> đ
              </div>
              <div style="margin-top:6px">
                <?php if(auth()->guard()->check()): ?>
                  <a class="btn" href="<?php echo e(route('bookings.seats', $st->id)); ?>">Chọn ghế</a>
                <?php else: ?>
                  <a class="btn btn-outline" href="<?php echo e(route('login')); ?>">Đăng nhập để đặt vé</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card"><span class="muted">Chưa có suất chiếu</span></div>
    <?php endif; ?>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/client/movies/show.blade.php ENDPATH**/ ?>