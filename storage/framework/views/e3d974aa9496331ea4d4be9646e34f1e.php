<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Rạp phim'); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
      :root{
        --bg:#0b1020; --surface:#0e162c; --text:#e5e7eb; --muted:#94a3b8; --accent:#6366f1; --accent-2:#22c55e; --border:#1f2937;
      }
      *{box-sizing:border-box}
      body{margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto; background:var(--bg); color:var(--text)}
      .container{max-width:1100px; margin:0 auto; padding:24px}
      header{position:sticky; top:0; z-index:10; backdrop-filter:saturate(180%) blur(8px); background:rgba(11,16,32,.7); border-bottom:1px solid var(--border)}
      .nav{display:flex; align-items:center; justify-content:space-between; padding:12px 24px}
      .brand{display:flex; align-items:center; gap:10px}
      .logo{width:28px; height:28px; border-radius:8px; background:linear-gradient(135deg, var(--accent), var(--accent-2))}
      .nav a{color:var(--text); text-decoration:none; margin-right:12px}
      .nav a.muted{color:var(--muted)}
      .btn, button.btn{display:inline-block; padding:8px 12px; border-radius:8px; background:var(--accent); color:#fff; text-decoration:none; border:1px solid rgba(99,102,241,.45)}
      .btn:hover, button.btn:hover{background:#4f46e5}
      .btn-outline{background:transparent; border:1px solid var(--border); color:var(--text)}
      .muted{color:var(--muted)}
      .grid{display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px}
      .card{background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:14px}
      .poster{width:100%; aspect-ratio:2/3; object-fit:cover; border-radius:10px; border:1px solid var(--border); background:#0a0f1f}
      .badge{display:inline-block; font-size:12px; padding:2px 8px; border-radius:999px; border:1px solid var(--border); color:var(--muted)}
      .row{display:flex; gap:10px; align-items:center; flex-wrap:wrap}
      footer{margin-top:40px; padding:20px 0; border-top:1px solid var(--border); color:var(--muted)}
      table{border-collapse:collapse; width:100%}
      th,td{border:1px solid var(--border); padding:8px}
      th{background:#0e162c}
      input,select,textarea{background:#0d152b; color:var(--text); border:1px solid var(--border); border-radius:8px; padding:8px}
    </style>
    <?php echo $__env->yieldContent('head'); ?>
  </head>
  <body>
    <header>
      <div class="nav container">
        <div class="brand">
          <div class="logo"></div>
          <a href="<?php echo e(route('movies.index')); ?>" style="text-decoration:none; color:var(--text); display: flex; align-items: center; gap: 10px;">
            <strong>Rạp phim</strong>
          </a>
        </div>
        <nav style="display: flex; align-items: center; gap: 20px;">
          <a href="<?php echo e(route('movies.index')); ?>" style="text-decoration: none; color: var(--text); padding: 8px 12px; border-radius: 6px;" 
             onmouseover="this.style.backgroundColor='rgba(99, 102, 241, 0.1)'" 
             onmouseout="this.style.backgroundColor='transparent'">
            Phim
          </a>
          
          <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('me.orders')); ?>" style="text-decoration: none; color: var(--text); padding: 8px 12px; border-radius: 6px;"
               onmouseover="this.style.backgroundColor='rgba(99, 102, 241, 0.1)'" 
               onmouseout="this.style.backgroundColor='transparent'">
              Đơn hàng
            </a>
            
            <div style="position: relative; display: inline-block;">
              <button style="background: none; border: none; color: var(--text); cursor: pointer; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; gap: 5px;"
                      onmouseover="this.style.backgroundColor='rgba(99, 102, 241, 0.1)'" 
                      onmouseout="this.style.backgroundColor='transparent'">
                <span>Tài khoản</span>
                <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <div style="position: absolute; right: 0; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 8px 0; min-width: 200px; margin-top: 8px; display: none; z-index: 1000; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                <div style="padding: 8px 16px; border-bottom: 1px solid var(--border);">
                  <div style="font-weight: 600;"><?php echo e(auth()->user()->fullname ?? auth()->user()->name); ?></div>
                  <div style="font-size: 0.9em; color: var(--muted);"><?php echo e(auth()->user()->email); ?></div>
                </div>
                <a href="<?php echo e(route('me.profile')); ?>" style="display: block; padding: 10px 16px; text-decoration: none; color: var(--text);"
                   onmouseover="this.style.backgroundColor='rgba(99, 102, 241, 0.1)'" 
                   onmouseout="this.style.backgroundColor='transparent'">
                  Hồ sơ của tôi
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin: 0;">
                  <?php echo csrf_field(); ?>
                  <button type="submit" style="width: 100%; text-align: left; padding: 10px 16px; background: none; border: none; color: var(--text); cursor: pointer;"
                          onmouseover="this.style.backgroundColor='rgba(99, 102, 241, 0.1)'" 
                          onmouseout="this.style.backgroundColor='transparent'">
                    Đăng xuất
                  </button>
                </form>
              </div>
            </div>
            
            <script>
              // Toggle dropdown menu
              document.querySelector('header button').addEventListener('click', function(e) {
                e.stopPropagation();
                const menu = this.nextElementSibling;
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
              });
              
              // Close dropdown when clicking outside
              document.addEventListener('click', function() {
                const dropdowns = document.querySelectorAll('header div > div');
                dropdowns.forEach(dropdown => {
                  if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                  }
                });
              });
              
              // Prevent dropdown from closing when clicking inside it
              document.querySelectorAll('header div > div').forEach(dropdown => {
                dropdown.addEventListener('click', function(e) {
                  e.stopPropagation();
                });
              });
            </script>
          <?php else: ?>
            <a class="btn btn-outline" href="<?php echo e(route('login')); ?>" style="text-decoration: none;">
              Đăng nhập
            </a>
            <a class="btn" href="<?php echo e(route('register')); ?>" style="text-decoration: none;">
              Đăng ký
            </a>
          <?php endif; ?>
        </nav>
      </div>
    </header>

    <main class="container">
      <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="container">
      <div>© <?php echo e(date('Y')); ?> Rạp phim • <span class="muted">Xem phim cùng bạn</span></div>
    </footer>
    <?php echo $__env->yieldContent('scripts'); ?>
    <?php if(auth()->check()): ?>
    <script src="<?php echo e(asset('js/account-status.js')); ?>"></script>
    <?php endif; ?>
  </body>
  </html>


<?php /**PATH C:\Users\Admin\datn-cinema\resources\views/client/layout.blade.php ENDPATH**/ ?>