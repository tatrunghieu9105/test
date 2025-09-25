<?php $__env->startSection('title', 'Quản lý tài khoản'); ?>
<?php $__env->startSection('page_title', 'Quản lý tài khoản'); ?>

<?php $__env->startSection('content'); ?>
  <style>
    .btn-sm {
      padding: 4px 8px;
      font-size: 12px;
    }
    .btn-danger {
      background-color: #ef4444;
      color: white;
    }
    .status-active { color: #10b981; }
    .status-inactive { color: #ef4444; }
  </style>

  <div class="card" style="margin-bottom:12px">
    <form class="filter" method="get" style="display:flex;gap:12px;flex-wrap:wrap">
      <div class="form-row">
        <label>Từ khóa</label>
        <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Tên hoặc email">
      </div>
      <div class="form-row">
        <label>Vai trò</label>
        <select name="role">
          <option value="">-- Tất cả --</option>
          <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($r->name); ?>" <?php if($role===$r->name): echo 'selected'; endif; ?>><?php echo e(ucfirst($r->name)); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit">Lọc</button>
        <a class="btn" href="<?php echo e(route('admin.users.index')); ?>" style="background:transparent;border-color:var(--border);color:var(--text)">Xóa lọc</a>
      </div>
    </form>
  </div>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Họ tên</th>
          <th>Email</th>
          <th>Trạng thái</th>
          <th>Vai trò</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($user->id); ?></td>
            <td><?php echo e($user->fullname); ?></td>
            <td><?php echo e($user->email); ?></td>
            <td>
              <span class="status-<?php echo e($user->is_active ? 'active' : 'inactive'); ?>">
                <?php echo e($user->is_active ? 'Đang hoạt động' : 'Đã khóa'); ?>

              </span>
            </td>
            <td><?php echo e(optional($user->role)->name ?? '—'); ?></td>
            <td>
              <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <form method="post" action="<?php echo e(route('admin.users.updateRole', $user)); ?>" class="inline">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('PUT'); ?>
                  <select name="role_id" style="min-width: 120px;">
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($role->id); ?>" <?php if($user->role_id === $role->id): echo 'selected'; endif; ?>>
                        <?php echo e(ucfirst($role->name)); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                  <button class="btn btn-sm" type="submit">Cập nhật</button>
                </form>
                
                <?php if(optional($user->role)->name !== 'admin' && $user->id !== auth()->id()): ?>
                  <form method="POST" action="<?php echo e(route('admin.users.updateStatus', $user)); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="is_active" value="<?php echo e($user->is_active ? '0' : '1'); ?>">
                    <button type="submit" class="btn btn-sm <?php echo e($user->is_active ? 'btn-danger' : ''); ?>">
                      <?php echo e($user->is_active ? 'Khóa' : 'Mở khóa'); ?>

                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="6" style="text-align: center">Không có dữ liệu</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($users->hasPages()): ?>
    <div style="margin-top:12px"><?php echo e($users->links()); ?></div>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/users/index.blade.php ENDPATH**/ ?>