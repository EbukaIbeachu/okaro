<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::startSession();
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ökaro and Associates</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/okaro/assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
<?php if ($user): ?>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand">Ökaro</div>
        </div>
        <nav class="sidebar-nav">
            <a href="/okaro/index.php" class="nav-item" title="Home"><i class="fas fa-home"></i> <span class="nav-label">Home</span></a>
            <?php if (Auth::hasRole(['admin', 'manager'])): ?>
                <a href="/okaro/index.php?route=admin/dashboard" class="nav-item" title="Dashboard"><i class="fas fa-tachometer-alt"></i> <span class="nav-label">Dashboard</span></a>
                <a href="/okaro/buildings.php" class="nav-item" title="Buildings"><i class="fas fa-building"></i> <span class="nav-label">Buildings</span></a>
                <a href="/okaro/units.php" class="nav-item" title="Units"><i class="fas fa-door-open"></i> <span class="nav-label">Units</span></a>
                <a href="/okaro/tenants.php" class="nav-item" title="Tenants"><i class="fas fa-users"></i> <span class="nav-label">Tenants</span></a>
                <a href="/okaro/rents.php" class="nav-item" title="Rents"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-label">Rents</span></a>
                <a href="/okaro/payments.php" class="nav-item" title="Payments"><i class="fas fa-money-bill-wave"></i> <span class="nav-label">Payments</span></a>
                <a href="/okaro/users.php" class="nav-item" title="Users"><i class="fas fa-user-cog"></i> <span class="nav-label">Users</span></a>
            <?php elseif (Auth::hasRole(['tenant'])): ?>
                <a href="/okaro/index.php?route=tenant/dashboard" class="nav-item" title="My Account"><i class="fas fa-user"></i> <span class="nav-label">My Account</span></a>
            <?php endif; ?>
            <a href="/okaro/logout.php" class="nav-item" title="Logout"><i class="fas fa-sign-out-alt"></i> <span class="nav-label">Logout</span></a>
        </nav>
    </aside>
<?php endif; ?>

<div class="main-content">
<header class="site-header">
    <div class="container header-inner">
        <div class="brand-mobile">Ökaro</div>
        <button class="sidebar-toggle" aria-label="Toggle sidebar"><i class="fas fa-bars"></i></button>
        <nav class="main-nav">
            <?php if (!$user): ?>
                <a href="/okaro/index.php">Home</a>
                <a href="/okaro/login.php">Login</a>
            <?php else: ?>
                <span class="user-greeting">Welcome, <?php echo htmlspecialchars($user['name'] ?? ''); ?></span>
            <?php endif; ?>
        </nav>
    </div>
</header>
<script>
    (function() {
        var app = document.querySelector('.app-wrapper');
        var btn = document.querySelector('.sidebar-toggle');
        if (!btn || !app) return;
        var state = 'expanded';
        try {
            state = localStorage.getItem('sidebarState') || 'expanded';
        } catch (e) {}
        function applyState(s) {
            app.classList.toggle('sidebar-hidden', s === 'hidden');
            app.classList.toggle('sidebar-collapsed', s === 'collapsed');
        }
        applyState(state);
        btn.addEventListener('click', function() {
            state = state === 'expanded' ? 'collapsed' : state === 'collapsed' ? 'hidden' : 'expanded';
            applyState(state);
            try {
                localStorage.setItem('sidebarState', state);
            } catch (e) {}
        });
    })();
</script>
<script>
// Enable drag-to-scroll on tables
(function(){
    var wrappers = document.querySelectorAll('.table-wrapper');
    wrappers.forEach(function(wrap){
        var isDown = false;
        var startX = 0;
        var scrollLeft = 0;
        wrap.addEventListener('mousedown', function(e){
            isDown = true;
            startX = e.pageX - wrap.offsetLeft;
            scrollLeft = wrap.scrollLeft;
        });
        wrap.addEventListener('mouseleave', function(){ isDown = false; });
        wrap.addEventListener('mouseup', function(){ isDown = false; });
        wrap.addEventListener('mousemove', function(e){
            if(!isDown) return;
            e.preventDefault();
            var x = e.pageX - wrap.offsetLeft;
            var walk = (x - startX) * 1; // scroll-fastness
            wrap.scrollLeft = scrollLeft - walk;
        });
        // Touch support
        var touchStartX = 0;
        var touchScrollLeft = 0;
        wrap.addEventListener('touchstart', function(e){
            var t = e.touches[0];
            touchStartX = t.pageX - wrap.offsetLeft;
            touchScrollLeft = wrap.scrollLeft;
        }, {passive:true});
        wrap.addEventListener('touchmove', function(e){
            var t = e.touches[0];
            var x = t.pageX - wrap.offsetLeft;
            var walk = (x - touchStartX) * 1;
            wrap.scrollLeft = touchScrollLeft - walk;
        }, {passive:true});
    });
})();
</script>
<script>
(function(){
    var tables = document.querySelectorAll('.table');
    tables.forEach(function(table){
        var wrapper = table.closest('.table-wrapper');
        var container = wrapper || table.parentNode;
        if (!container || !container.parentNode) return;
        if (container.parentNode.querySelector('.table-search') || container.parentNode.querySelector('input[type="text"][placeholder*="Search"]')) return;
        var input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'Search...';
        input.className = 'table-search';
        input.style.cssText = 'margin:8px 0; padding:8px; border:1px solid #ccc; border-radius:4px; width:100%; max-width:320px;';
        container.parentNode.insertBefore(input, container);
        input.addEventListener('input', function(){
            var filter = input.value.toLowerCase();
            var body = table.tBodies && table.tBodies[0] ? table.tBodies[0] : null;
            var rows = body ? body.rows : table.getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var cells = row.getElementsByTagName('td');
                if (cells.length === 0) continue;
                var found = false;
                for (var j = 0; j < cells.length; j++) {
                    var txt = (cells[j].textContent || '').toLowerCase();
                    if (txt.indexOf(filter) > -1) { found = true; break; }
                }
                row.style.display = found ? '' : 'none';
            }
        });
    });
})();
</script>
<main class="site-main container">
