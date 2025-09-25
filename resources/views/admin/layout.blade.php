<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Bảng điều khiển')</title>
  <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
  <style>
    :root{
      --bg:#0f172a; --bg-soft:#111827; --card:#0b1227; --text:#e5e7eb; --muted:#94a3b8;
      --accent:#6366f1; --border:#1f2937; --ok:#10b981; --warn:#f59e0b; --err:#ef4444;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto;background:var(--bg);color:var(--text)}
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
    .card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px}
    .flash {
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
    }
    .flash.success {
      background: rgba(16, 185, 129, 0.08);
      border: 1px solid rgba(16, 185, 129, 0.2);
      color: #10b981;
    }
    .flash.error {
      background: rgba(239, 68, 68, 0.08);
      border: 1px solid rgba(239, 68, 68, 0.2);
      color: #ef4444;
    }
    table{border-collapse:collapse;width:100%}
    th,td{border:1px solid var(--border);padding:8px}
    th{background:#0e162c}
    input,select,textarea,button{background:#0d152b;color:var(--text);border:1px solid var(--border);border-radius:8px;padding:8px}
    a.btn, button.btn{display:inline-block;padding:8px 12px;border-radius:8px;background:var(--accent);border:1px solid rgba(99,102,241,.5);color:#fff;text-decoration:none}
    a.btn:hover, button.btn:hover{background:#4f46e5}
    form.inline{display:inline}
    .actions a{margin-right:8px}
    /* Form helpers */
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .form-row{display:flex;flex-direction:column;margin-bottom:12px}
    .form-row label{font-size:13px;color:var(--muted);margin-bottom:6px}
    .form-actions{display:flex;gap:10px;align-items:center;margin-top:12px}
    .hint{font-size:12px;color:var(--muted)}
    .section-title{margin:10px 0 6px 0;color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.06em}
    select[multiple]{min-height:160px}
    @media (max-width: 900px){ .layout{grid-template-columns:1fr} .sidebar{position:relative;height:auto} }
  </style>
  @yield('head')
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="brand"><div class="logo"></div><a href="/admin"><h1>Quản trị rạp</h1></a></div>
    <div class="user">Xin chào, <strong>{{ auth()->user()->fullname ?? 'Admin' }}</strong></div>
    <nav class="menu">
      <h3>Chung</h3>
      <a href="/movies"><span class="icon">🎬</span><span>Trang xem phim</span></a>
      <a href="/admin/checkin"><span class="icon">🎟️</span><span>Check-in (QR/Barcode)</span></a>
      @staff
        <h3>Nhân viên</h3>
        <a href="/admin/rooms"><span class="icon">🏟️</span><span>Phòng chiếu</span></a>
        <a href="/admin/showtimes"><span class="icon">🕒</span><span>Suất chiếu</span></a>
        <a href="/admin/tickets"><span class="icon">🧾</span><span>Đặt vé</span></a>
      @endstaff
      @manager
        <h3>Quản lý</h3>
        <a href="/admin/movies"><span class="icon">🎞️</span><span>Quản lý phim</span></a>
        <a href="/admin/actors"><span class="icon">🧑‍🎤</span><span>Quản lý diễn viên</span></a>
        <a href="/admin/categories"><span class="icon">🏷️</span><span>Quản lý danh mục</span></a>
        <a href="/admin/discounts"><span class="icon">💸</span><span>Quản lý khuyến mãi</span></a>
        <a href="/admin/combos"><span class="icon">🍿</span><span>Quản lý combo</span></a>
        <a href="/admin/stats"><span class="icon">📈</span><span>Thống kê</span></a>
        <a href="/admin/users"><span class="icon">👥</span><span>Quản lý tài khoản</span></a>
        <a href="/admin/logs"><span class="icon">📚</span><span>Nhật ký thao tác</span></a>
      @endmanager
    </nav>
  </aside>
  <main class="content">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <h2 style="margin:0">@yield('page_title', 'Bảng điều khiển')</h2>
      <div class="actions">
        <a href="/logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Đăng xuất</a>
        <form id="logout-form" method="post" action="/logout" style="display:none">@csrf</form>
      </div>
    </div>
    @if(session('success'))
      <div class="flash success">
        <span>✓</span>
        <span>{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="flash error">
        <span>!</span>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    @yield('content')
  </main>
</div>

</body>
</html>
