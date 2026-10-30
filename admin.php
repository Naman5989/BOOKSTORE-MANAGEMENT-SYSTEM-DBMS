<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Quick stats
$total_books   = $conn->query("SELECT COUNT(*) AS c FROM Books")->fetch_assoc()['c'];
$total_users   = $conn->query("SELECT COUNT(*) AS c FROM Users WHERE Role='customer'")->fetch_assoc()['c'];
$total_orders  = $conn->query("SELECT COUNT(*) AS c FROM Orders")->fetch_assoc()['c'];
$pending_orders = $conn->query("SELECT COUNT(*) AS c FROM Orders WHERE Status='Pending'")->fetch_assoc()['c'];
$pending_payments = $conn->query("SELECT COUNT(*) AS c FROM Payment WHERE Payment_Status='Pending'")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT COALESCE(SUM(Amount),0) AS r FROM Payment WHERE Payment_Status='Success'")->fetch_assoc()['r'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Bookstore</title>
<link rel="stylesheet" href="style.css">
<style>
    .admin-nav { background: #0F172A; }
    .admin-nav a { color: #94A3B8; }
    .admin-nav a:hover, .admin-nav a.active { color: #60A5FA; }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .stat-card .stat-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-card .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-color); }
    .stat-card .stat-icon { font-size: 1.8rem; }
    .admin-menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.5rem;
    }
    .admin-menu-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.75rem;
        text-decoration: none;
        color: var(--primary-color);
        box-shadow: var(--shadow-sm);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .admin-menu-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        border-color: var(--accent-color);
        color: var(--accent-color);
    }
    .admin-menu-card .menu-icon { font-size: 2.2rem; }
    .admin-menu-card .menu-title { font-size: 1.1rem; font-weight: 700; }
    .admin-menu-card .menu-desc { font-size: 0.85rem; color: var(--text-muted); }
    <?php if ($pending_orders > 0): ?>
    .pending-badge { background: #FEF3C7; color: #92400E; font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 700; }
    <?php endif; ?>
</style>
</head>
<body>

<header class="admin-nav" style="background:#0F172A;">
    <h1 style="color:#F1F5F9;">🛠️ Admin Panel</h1>
    <nav>
        <a href="admin.php" class="active" style="color:#60A5FA;">Dashboard</a>
        <a href="add_book.php" style="color:#94A3B8;">Add Book</a>
        <a href="manage_books.php" style="color:#94A3B8;">Books</a>
        <a href="view_orders.php" style="color:#94A3B8;">Orders</a>
        <a href="manage_payments.php" style="color:#94A3B8;">Payments <?php if ($pending_payments > 0) echo "<span style='background:#F59E0B;color:white;padding:0.1rem 0.45rem;border-radius:999px;font-size:0.75rem;font-weight:700;'>$pending_payments</span>"; ?></a>
        <a href="view_users.php" style="color:#94A3B8;">Users</a>
        <a href="index.php" style="color:#94A3B8;">← Store</a>
        <a href="logout.php" style="color:#F87171;">Logout (<?php echo htmlspecialchars($_SESSION['name']); ?>)</a>
    </nav>
</header>

<div class="container">
    <div class="section-title" style="margin-bottom: 1.5rem;">
        <span>Dashboard Overview</span>
        <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo date('l, F j Y'); ?></span>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-label">Total Books</div>
            <div class="stat-value"><?php echo number_format($total_books); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-label">Customers</div>
            <div class="stat-value"><?php echo number_format($total_users); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value"><?php echo number_format($total_orders); ?></div>
        </div>
        <div class="stat-card" style="border-color: #FCD34D;">
            <div class="stat-icon">⏳</div>
            <div class="stat-label">Pending Orders</div>
            <div class="stat-value" style="color: #D97706;"><?php echo number_format($pending_orders); ?></div>
        </div>
        <div class="stat-card" style="border-color: #F59E0B; background: #FFFDF5;">
            <div class="stat-icon">💳</div>
            <div class="stat-label">Pending Payments</div>
            <div class="stat-value" style="color: #D97706;"><?php echo number_format($pending_payments); ?></div>
        </div>
        <div class="stat-card" style="border-color: #6EE7B7;">
            <div class="stat-icon">💰</div>
            <div class="stat-label">Verified Revenue</div>
            <div class="stat-value" style="color: #059669; font-size: 1.5rem;">₹<?php echo number_format($total_revenue, 2); ?></div>
        </div>
    </div>

    <div class="section-title">
        <span>Quick Actions</span>
    </div>

    <div class="admin-menu-grid">
        <a href="manage_payments.php" class="admin-menu-card" style="border-color: #F59E0B;">
            <div class="menu-icon">💳</div>
            <div class="menu-title">Manage Customer Payments <?php if ($pending_payments > 0) echo "<span style='background:#F59E0B;color:white;padding:0.15rem 0.5rem;border-radius:999px;font-size:0.75rem;font-weight:700;'>$pending_payments pending</span>"; ?></div>
            <div class="menu-desc">Review and verify payments completed by customers when they place orders, mark as paid or failed.</div>
        </a>
        <a href="view_orders.php" class="admin-menu-card">
            <div class="menu-icon">📦</div>
            <div class="menu-title">Manage Orders <?php if ($pending_orders > 0) echo "<span class='pending-badge'>$pending_orders pending</span>"; ?></div>
            <div class="menu-desc">View all orders and update their status from Pending to Confirmed, Shipped or Delivered.</div>
        </a>
        <a href="add_book.php" class="admin-menu-card">
            <div class="menu-icon">➕</div>
            <div class="menu-title">Add New Book</div>
            <div class="menu-desc">Add a new book title, author, category, price and stock to the catalog.</div>
        </a>
        <a href="manage_books.php" class="admin-menu-card">
            <div class="menu-icon">📖</div>
            <div class="menu-title">Manage Books</div>
            <div class="menu-desc">Edit price, stock, or delete existing books from the catalog.</div>
        </a>
        <a href="view_users.php" class="admin-menu-card">
            <div class="menu-icon">👥</div>
            <div class="menu-title">Manage Users</div>
            <div class="menu-desc">View all registered customers and manage their accounts.</div>
        </a>
    </div>
</div>

</body>
</html>
