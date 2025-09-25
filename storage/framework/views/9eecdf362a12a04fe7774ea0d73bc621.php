<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <style>
        :root{
            --bg:#0f172a;        /* slate-900 */
            --bg-soft:#111827;   /* gray-900 */
            --card:#0b1227;      /* custom dark */
            --text:#e5e7eb;      /* gray-200 */
            --muted:#94a3b8;     /* slate-400 */
            --accent:#6366f1;    /* indigo-500 */
            --accent-soft:#4f46e5;
            --border:#1f2937;    /* gray-800 */
            --ok:#10b981;        /* emerald-500 */
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto; background:var(--bg); color:var(--text);}
        .layout{display:grid; grid-template-columns:260px 1fr; min-height:100vh}
        .sidebar{background:var(--bg-soft); border-right:1px solid var(--border); padding:20px 16px; position:sticky; top:0; height:100vh}
        .brand{display:flex;align-items:center;gap:10px;margin-bottom:18px}
        .brand .logo{width:36px;height:36px;border-radius:8px; background:linear-gradient(135deg,var(--accent),#22c55e)}
        .brand h1{font-size:18px;margin:0}
        .user{font-size:13px;color:var(--muted);margin-bottom:16px}
        .menu h3{font-size:12px;letter-spacing:.06em;color:var(--muted);text-transform:uppercase;margin:18px 8px 8px}
        .menu a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;color:var(--text);text-decoration:none;border:1px solid transparent}
        .menu a:hover{background:rgba(99,102,241,.12);border-color:rgba(99,102,241,.3)}
        .menu .icon{width:18px;height:18px;display:inline-block;opacity:.9}
        .content{padding:28px}
        .cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
        .card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px}
        .card h4{margin:0 0 6px 0;font-size:16px}
        .card p{margin:0;color:var(--muted);font-size:13px}
        .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
        .topbar .actions a{color:var(--muted);text-decoration:none;font-size:14px}
        .topbar .actions a:hover{color:var(--text)}
        @media (max-width: 900px){ .layout{grid-template-columns:1fr} .sidebar{position:relative;height:auto} }
    </style>
    <script>
      // optional: future toggle theme/menu
    </script>
    </head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="logo"></div>
            <a href="/admin"><h1>Quản trị rạp</h1></a>
        </div>
        <div class="user">Xin chào, <strong><?php echo e(auth()->user()->fullname ?? 'Admin'); ?></strong></div>

        <nav class="menu">
            <h3>Chung</h3>
            <a href="/movies"><span class="icon">🎬</span><span>Trang xem phim</span></a>
            <a href="/admin/checkin"><span class="icon">🎟️</span><span>Check-in (QR/Barcode)</span></a>

            <?php if (\Illuminate\Support\Facades\Blade::check('staff')): ?>
                <h3>Nhân viên</h3>
                <a href="/admin/rooms"><span class="icon">🏟️</span><span>Phòng chiếu</span></a>
                <a href="/admin/showtimes"><span class="icon">🕒</span><span>Suất chiếu</span></a>
                <a href="/admin/tickets"><span class="icon">🧾</span><span>Đặt vé</span></a>
            <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Blade::check('manager')): ?>
                <h3>Quản lý</h3>
                <a href="/admin/movies"><span class="icon">🎞️</span><span>Quản lý phim</span></a>
                <a href="/admin/actors"><span class="icon">🧑‍🎤</span><span>Quản lý diễn viên</span></a>
                <a href="/admin/categories"><span class="icon">🏷️</span><span>Quản lý danh mục</span></a>
                <a href="/admin/discounts"><span class="icon">💸</span><span>Quản lý khuyến mãi</span></a>
                <a href="/admin/combos"><span class="icon">🍿</span><span>Quản lý combo</span></a>
                <a href="/admin/stats"><span class="icon">📈</span><span>Thống kê</span></a>
                <a href="/admin/users"><span class="icon">👥</span><span>Quản lý tài khoản</span></a>
                <a href="/admin/logs"><span class="icon">📚</span><span>Nhật ký thao tác</span></a>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="content">
        <div class="topbar">
            <h2>Bảng điều khiển</h2>
            <div class="actions">
                <a href="/logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Đăng xuất</a>
                <form id="logout-form" method="post" action="/logout" style="display:none"><?php echo csrf_field(); ?></form>
            </div>
        </div>

        <section class="cards">
            <div class="card">
                <a href="/admin/showtimes"><h4>Quản lý suất chiếu</h4></a>
                <p>Quản lý lịch chiếu</p>
            </div>
            <div class="card">
                <a href="/admin/rooms"><h4>Quản lý phòng</h4></a>
                <p>Cấu hình phòng chiếu</p>
            </div>
            <div class="card">
                <a href="/admin/tickets"><h4>Bán vé</h4></a>
                <p>Tạo và xử lý vé cho khách.</p>
            </div>
            <?php if (\Illuminate\Support\Facades\Blade::check('manager')): ?>
            <div class="card">
                <a href="/admin/movies"><h4>Dữ liệu nội dung</h4></a>
                <p>Quản lý phim, diễn viên, danh mục, combo, khuyến mãi.</p>
            </div>
            <div class="card">
                <a href="/admin/stats"><h4>Báo cáo</h4></a>
                <p>Xem thống kê doanh thu và hiệu suất.</p>
            </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>


<?php /**PATH C:\Users\Admin\datn-cinema\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>