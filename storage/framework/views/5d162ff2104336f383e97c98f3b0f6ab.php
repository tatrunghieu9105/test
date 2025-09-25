

<?php $__env->startSection('title','Sửa suất chiếu'); ?>
<?php $__env->startSection('page_title','Sửa suất chiếu #'.$showtime->id); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <form method="post" action="<?php echo e(route('admin.showtimes.update', $showtime)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-grid">
                <div class="form-row">
                    <label>Phim</label>
                    <select name="movie_id" required>
                        <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>" <?php echo e($showtime->movie_id==$m->id?'selected':''); ?>><?php echo e($m->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-row">
                    <label>Phòng</label>
                    <select name="room_id" required>
                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($r->id); ?>" <?php echo e($showtime->room_id==$r->id?'selected':''); ?>><?php echo e($r->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Bắt đầu</label>
                    <input type="datetime-local" name="start_time" value="<?php echo e(str_replace(' ', 'T', $showtime->start_time)); ?>" required>
                </div>
                <div class="form-row">
                    <label>Kết thúc</label>
                    <input type="datetime-local" name="end_time" value="<?php echo e(str_replace(' ', 'T', $showtime->end_time)); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <label>Giá</label>
                <input type="number" name="price" min="0" step="1000" value="<?php echo e($showtime->price); ?>" required>
            </div>

            <div class="form-actions">
                <button class="btn" type="submit">Cập nhật</button>
                <a class="btn" href="<?php echo e(route('admin.showtimes.index')); ?>" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>

    <?php if($errors->any()): ?>
        <div class="flash error" style="margin-top:8px;"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
  const movieDurations = {
    <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php echo e($m->id); ?>: <?php echo e((int)($m->duration ?? 0)); ?>,
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  };
  const movieSel = document.querySelector('select[name="movie_id"]');
  const roomSel  = document.querySelector('select[name="room_id"]');
  const startInp = document.querySelector('input[name="start_time"]');
  const endInp   = document.querySelector('input[name="end_time"]');

  function addMinutes(isoLocal, minutes){
    if(!isoLocal) return '';
    const d = new Date(isoLocal);
    if (isNaN(d.getTime())) return '';
    d.setMinutes(d.getMinutes() + minutes);
    const pad = n => String(n).padStart(2,'0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
  }

  function autoCalcEnd(){
    const movieId = movieSel.value;
    const dur = movieDurations[movieId] || 0;
    const startVal = startInp.value;
    if (dur > 0 && startVal){
      endInp.value = addMinutes(startVal, dur);
    }
  }

  async function checkOverlap(){
    const params = new URLSearchParams({
      room_id: roomSel.value,
      start_time: startInp.value.replace('T',' '),
      end_time: endInp.value.replace('T',' '),
      exclude_id: '<?php echo e($showtime->id); ?>'
    });
    if(!params.get('room_id') || !params.get('start_time') || !params.get('end_time')) return;
    try{
      const res = await fetch(`<?php echo e(route('admin.showtimes.api')); ?>?${params.toString()}`, {headers:{'Accept':'application/json'}});
      if(!res.ok) return;
      const data = await res.json();
      endInp.setCustomValidity('');
      startInp.setCustomValidity('');
      if(data.overlap){
        startInp.setCustomValidity(data.message);
        endInp.setCustomValidity(data.message);
      }
    }catch(e){/* ignore */}
  }

  movieSel.addEventListener('change', () => { autoCalcEnd(); checkOverlap(); });
  startInp.addEventListener('change', () => { autoCalcEnd(); checkOverlap(); });
  endInp.addEventListener('change', checkOverlap);
  roomSel.addEventListener('change', checkOverlap);

  // initial validation
  checkOverlap();
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/showtimes/edit.blade.php ENDPATH**/ ?>