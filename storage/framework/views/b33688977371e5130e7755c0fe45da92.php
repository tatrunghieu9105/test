

<?php $__env->startSection('title', 'Chọn ghế'); ?>

<?php $__env->startSection('content'); ?>
<h1 style="margin:0 0 6px 0">Phòng <?php echo e(optional($showtime->room)->name); ?> | <?php echo e(\Carbon\Carbon::parse($showtime->start_time)->format('d/m H:i')); ?></h1>
<div class="muted" style="margin-bottom:8px">Giá vé: <?php echo e(number_format($showtime->price,0,',','.')); ?> đ / ghế</div>

<form id="booking-form" method="post" action="<?php echo e(route('booking.store')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="showtime_id" value="<?php echo e($showtime->id); ?>">

    <div class="card" style="margin-bottom:12px">
      <div class="row" style="gap:12px; align-items:flex-start">
        <div>
          <div class="screen" style="width:100%;max-width:420px;height:8px;background:linear-gradient(90deg,#334155,#64748b);border-radius:999px;margin:0 auto 10px auto"></div>
          <div class="grid" style="display:grid;grid-template-columns:repeat(10,40px);gap:6px;justify-content:center">
            <?php $__currentLoopData = $seats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="seat <?php echo e($seat->type === 'VIP' ? 'vip' : ''); ?> <?php echo e($seat->is_taken ? 'taken' : ''); ?>"
                   data-id="<?php echo e($seat->id); ?>" data-code="<?php echo e($seat->code); ?>" title="<?php echo e($seat->code); ?>">
                <?php echo e($seat->code); ?>

              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <div class="row" style="gap:10px; margin-top:10px">
            <span class="badge" style="background:#0e162c">Trống</span>
            <span class="badge" style="background:#fde68a;color:#111827">VIP</span>
            <span class="badge" style="background:#fca5a5;color:#111827">Đã đặt</span>
            <span class="badge" id="legend-selected" style="background:#4f46e5">Đang chọn</span>
          </div>
        </div>
        <div class="card" style="min-width:260px">
          <h3 style="margin:0 0 8px 0">Tóm tắt</h3>
          <div class="muted" style="font-size:14px">Phim: <?php echo e(optional($showtime->movie)->title); ?></div>
          <div class="muted" style="font-size:14px">Thời gian: <?php echo e(\Carbon\Carbon::parse($showtime->start_time)->format('d/m H:i')); ?></div>
          <div class="muted" style="font-size:14px">Phòng: <?php echo e(optional($showtime->room)->name); ?></div>
          <div id="selected-seats" class="muted" style="margin:8px 0; font-size:14px">Ghế: —</div>
          <div class="row" style="margin:8px 0">
            <select name="discount_code" id="discount-select">
              <option value="" data-type="" data-value="0">-- Chọn mã giảm giá (tuỳ chọn) --</option>
              <?php $__currentLoopData = $discountCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($d->code); ?>" data-type="<?php echo e($d->type); ?>" data-value="<?php echo e($d->value); ?>">
                  <?php echo e($d->code); ?> (<?php echo e($d->type==='percent' ? 'Giảm '.$d->value.'%' : 'Giảm '.number_format($d->value,0,',','.') .' đ'); ?>)
                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="row" style="margin:8px 0">
            <select name="combo_id" id="combo-select">
                <option value="" data-price="0">-- Chọn combo (tuỳ chọn) --</option>
                <?php $__currentLoopData = $combos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" data-price="<?php echo e($c->price); ?>"><?php echo e($c->name); ?> (<?php echo e(number_format($c->price,0,',','.')); ?> đ)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div style="border-top:1px solid var(--border); margin:10px 0"></div>
          <div id="summary" style="font-size:14px">
            <div class="row" style="justify-content:space-between"><span>Số ghế</span><strong id="sum-count">0</strong></div>
            <div class="row" style="justify-content:space-between"><span>Tạm tính</span><strong id="sum-sub">0 đ</strong></div>
            <div class="row" style="justify-content:space-between"><span>Giảm giá</span><strong id="sum-discount">-0 đ</strong></div>
            <div class="row" style="justify-content:space-between"><span>Combo</span><strong id="sum-combo">0 đ</strong></div>
            <div class="row" style="justify-content:space-between"><span>Tổng</span><strong id="sum-total">0 đ</strong></div>
          </div>
          <div class="row" style="margin-top:10px">
            <button type="submit" class="btn">Đặt vé</button>
          </div>
        </div>
      </div>
    </div>
</form>

<style>
.seat{width:40px;height:40px;line-height:40px;text-align:center;border:1px solid var(--border);border-radius:6px;cursor:pointer;background:#0d152b}
.seat.vip{background:#fde68a;color:#111827}
.seat.taken{background:#fca5a5;cursor:not-allowed;color:#111827}
.seat.selected{outline:2px solid #4f46e5}
</style>
<script>
const basePrice = <?php echo e((int)$showtime->price); ?>;
const selected = new Map(); // id -> code
const formatter = n => new Intl.NumberFormat('vi-VN').format(n) + ' đ';

function recalc(){
  const count = selected.size;
  const sub = count * basePrice;
  const comboOpt = document.getElementById('combo-select').selectedOptions[0];
  const combo = Number(comboOpt?.dataset.price || 0);
  const dOpt = document.getElementById('discount-select').selectedOptions[0];
  const dtype = dOpt?.dataset.type || '';
  const dval = Number(dOpt?.dataset.value || 0);
  let discount = 0;
  if (dtype === 'percent') discount = Math.round(sub * dval / 100);
  else if (dtype === 'amount') discount = dval;
  if (discount > sub) discount = sub;
  const total = sub - discount + combo;
  document.getElementById('sum-count').textContent = count;
  document.getElementById('sum-sub').textContent = formatter(sub);
  document.getElementById('sum-discount').textContent = '-' + formatter(discount);
  document.getElementById('sum-combo').textContent = formatter(combo);
  document.getElementById('sum-total').textContent = formatter(total);
  const seatsStr = count ? Array.from(selected.values()).join(', ') : '—';
  document.getElementById('selected-seats').textContent = 'Ghế: ' + seatsStr;
}

document.querySelectorAll('.seat').forEach(el => {
  if (!el.classList.contains('taken')) {
    el.addEventListener('click', () => {
      const id = el.getAttribute('data-id');
      const code = el.getAttribute('data-code');
      if (el.classList.toggle('selected')) { selected.set(id, code); }
      else { selected.delete(id); }
      recalc();
    });
  }
});

document.getElementById('discount-select').addEventListener('change', recalc);
document.getElementById('combo-select').addEventListener('change', recalc);

document.getElementById('booking-form').addEventListener('submit', (e) => {
  if (selected.size === 0) {
    e.preventDefault();
    alert('Vui lòng chọn ít nhất 1 ghế');
    return;
  }
  // remove old seat_ids
  document.querySelectorAll('input[name="seat_ids[]"]').forEach(i => i.remove());
  for (const id of selected.keys()) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'seat_ids[]';
    input.value = id;
    e.target.appendChild(input);
  }
});

recalc();
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('client.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/client/bookings/seats.blade.php ENDPATH**/ ?>