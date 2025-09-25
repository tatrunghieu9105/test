<!DOCTYPE html>
<html>
<body style="font-family:system-ui">
<h2>Cảm ơn bạn đã đặt vé</h2>
<p>Chi tiết vé:</p>
<ul>
<?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li>
        Mã: <?php echo e($t->code); ?> | Suất: <?php echo e(optional($t->showtime)->start_time); ?> | Ghế: <?php echo e(optional($t->seat)->code); ?> | Giá: <?php echo e(number_format($t->price,0,',','.')); ?> đ
        <br>
        <span>QR vé:</span><br>
        <?php
            $qrData = $t->code; // Chỉ chứa mã vé
        ?>
        <?php echo QrCode::size(120)->generate($qrData); ?>

    </li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<p><strong>Tổng:</strong> <?php echo e(number_format($total,0,',','.')); ?> đ</p>
<p>Chúc bạn xem phim vui vẻ!</p>
</body>
</html>


<?php /**PATH C:\Users\Admin\datn-cinema\resources\views/emails/ticket_paid.blade.php ENDPATH**/ ?>