

<?php $__env->startSection('title', 'Danh sách phim'); ?>

<?php $__env->startSection('content'); ?>
<h1 style="margin:0 0 12px 0">Đang chiếu</h1>

<form method="get" class="row" style="margin:12px 0;gap:12px">
    <input type="text" name="q" value="<?php echo e($keyword ?? ''); ?>" placeholder="Tìm theo tên...">
    <select name="category_id">
        <option value="">Tất cả thể loại</option>
        <?php $__currentLoopData = ($categories ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if(($categoryId ?? '')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn">Lọc</button>
    <a href="<?php echo e(route('movies.index')); ?>" class="btn btn-outline">Reset</a>
    <?php if(isset($keyword) && $keyword !== ''): ?>
      <span class="badge">Từ khóa: <?php echo e($keyword); ?></span>
    <?php endif; ?>
  </form>

<div class="grid">
    <?php if(isset($movies)): ?>
    <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="card">
        <img class="poster" src="<?php echo e($movie->poster_url ?: 'https://picsum.photos/400/600?blur=3'); ?>" alt="<?php echo e($movie->title); ?>">
        <div class="row" style="justify-content:space-between; margin-top:8px">
          <span class="badge"><?php echo e(optional($movie->category)->name ?? '—'); ?></span>
          <span class="muted"><?php echo e($movie->release_date); ?></span>
        </div>
        <h3 style="margin:8px 0 6px 0; font-size:16px"><?php echo e($movie->title); ?></h3>
        <div class="row" style="justify-content:space-between">
          <a class="btn" href="<?php echo e(route('movies.show', $movie)); ?>">Chi tiết</a>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
  </div>

  <?php if(isset($movies)): ?>
      <?php if($movies instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
          <div style="margin-top:16px;">
              <?php echo e($movies->links()); ?>

          </div>
      <?php endif; ?>
  <?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('client.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/client/movies/index.blade.php ENDPATH**/ ?>