<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message  = "";
$msg_type = "success";

// ---- Action: Complete Payment (Admin verifies payment is completed) ----
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_payment') {
    $payment_id = intval($_POST['payment_id'] ?? 0);
    $txn_ref    = trim($_POST['transaction_ref'] ?? '');

    if ($payment_id > 0) {
        // Fetch current payment details
        $stmt = $conn->prepare("SELECT P.Order_ID, P.Amount, O.Status AS Order_Status FROM Payment P JOIN Orders O ON P.Order_ID = O.Order_ID WHERE P.Payment_ID = ?");
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $pay_data = $stmt->get_result()->fetch_assoc();

        if ($pay_data) {
            $order_id = $pay_data['Order_ID'];
            $amount   = $pay_data['Amount'];

            // Update Payment to Success
            if ($txn_ref !== '') {
                $up_stmt = $conn->prepare("UPDATE Payment SET Payment_Status = 'Success', Transaction_Ref = ? WHERE Payment_ID = ?");
                $up_stmt->bind_param("si", $txn_ref, $payment_id);
            } else {
                $up_stmt = $conn->prepare("UPDATE Payment SET Payment_Status = 'Success' WHERE Payment_ID = ?");
                $up_stmt->bind_param("i", $payment_id);
            }
            $up_stmt->execute();

            // Automatically confirm order if it was Pending
            if ($pay_data['Order_Status'] === 'Pending') {
                $conn->query("UPDATE Orders SET Status = 'Confirmed' WHERE Order_ID = $order_id");
                $message = "✅ Payment #$payment_id (₹" . number_format($amount, 2) . ") for Order #$order_id marked as <strong>COMPLETED</strong>. Order has been moved to <strong>Confirmed</strong>.";
            } else {
                $message = "✅ Payment #$payment_id (₹" . number_format($amount, 2) . ") marked as <strong>COMPLETED</strong>.";
            }
        } else {
            $message  = "Payment record not found.";
            $msg_type = "error";
        }
    }
}

// ---- Action: Mark Payment Failed ----
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fail_payment') {
    $payment_id = intval($_POST['payment_id'] ?? 0);
    if ($payment_id > 0) {
        $stmt = $conn->prepare("SELECT Order_ID FROM Payment WHERE Payment_ID = ?");
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $order_id = $row['Order_ID'];
            $conn->query("UPDATE Payment SET Payment_Status = 'Failed' WHERE Payment_ID = $payment_id");
            if (isset($_POST['cancel_order']) && $_POST['cancel_order'] === '1') {
                $conn->query("UPDATE Orders SET Status = 'Cancelled' WHERE Order_ID = $order_id AND Status = 'Pending'");
            }
            $message = "❌ Payment #$payment_id for Order #$order_id marked as <strong>FAILED</strong>.";
        }
    }
}

// ---- Action: Reset Payment to Pending ----
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_payment') {
    $payment_id = intval($_POST['payment_id'] ?? 0);
    if ($payment_id > 0) {
        $conn->query("UPDATE Payment SET Payment_Status = 'Pending' WHERE Payment_ID = $payment_id");
        $message = "⏳ Payment #$payment_id status reset to <strong>Pending</strong>.";
    }
}

// ---- Action: Quick Status Update from Dropdown ----
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status_quick') {
    $payment_id = intval($_POST['payment_id'] ?? 0);
    $new_status = $_POST['new_payment_status'] ?? '';
    $allowed = ['Pending', 'Success', 'Failed'];

    if ($payment_id > 0 && in_array($new_status, $allowed)) {
        $stmt = $conn->prepare("UPDATE Payment SET Payment_Status = ? WHERE Payment_ID = ?");
        $stmt->bind_param("si", $new_status, $payment_id);
        $stmt->execute();

        if ($new_status === 'Success') {
            $conn->query("UPDATE Orders O JOIN Payment P ON P.Order_ID = O.Order_ID SET O.Status = 'Confirmed' WHERE P.Payment_ID = $payment_id AND O.Status = 'Pending'");
        }
        $message = "💳 Payment #$payment_id status updated to <strong>$new_status</strong>.";
    }
}

// ---- Stats Query ----
$stats = $conn->query("
    SELECT
        COUNT(*) AS total_txns,
        SUM(CASE WHEN Payment_Status = 'Pending' THEN 1 ELSE 0 END) AS pending_txns,
        SUM(CASE WHEN Payment_Status = 'Success' THEN 1 ELSE 0 END) AS success_txns,
        SUM(CASE WHEN Payment_Status = 'Failed'  THEN 1 ELSE 0 END) AS failed_txns,
        COALESCE(SUM(CASE WHEN Payment_Status = 'Success' THEN Amount ELSE 0 END), 0) AS total_revenue
    FROM Payment
")->fetch_assoc();

// ---- Filter & Search Handling ----
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$where_clauses = [];
$params = [];
$types  = "";

if ($filter === 'pending') {
    $where_clauses[] = "P.Payment_Status = 'Pending'";
} elseif ($filter === 'success') {
    $where_clauses[] = "P.Payment_Status = 'Success'";
} elseif ($filter === 'failed') {
    $where_clauses[] = "P.Payment_Status = 'Failed'";
}

if ($search !== '') {
    $where_clauses[] = "(U.Name LIKE ? OR U.Email LIKE ? OR P.Payment_Method LIKE ? OR P.Transaction_Ref LIKE ? OR CAST(O.Order_ID AS CHAR) = ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search;
    $types .= "sssss";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// ---- Fetch Payments List with Order, Customer, and Book details ----
$query = "
    SELECT 
        P.Payment_ID, P.Order_ID, P.Payment_Date, P.Amount, P.Payment_Method, 
        P.Payment_Status, P.Transaction_Ref,
        O.Order_Date, O.Status AS Order_Status,
        U.User_ID, U.Name AS Customer_Name, U.Email AS Customer_Email, U.Phone AS Customer_Phone,
        GROUP_CONCAT(CONCAT(B.Title, ' (x', OD.Quantity, ')') SEPARATOR ', ') AS Items_Summary
    FROM Payment P
    JOIN Orders O ON P.Order_ID = O.Order_ID
    JOIN Users U ON O.User_ID = U.User_ID
    LEFT JOIN Order_Details OD ON OD.Order_ID = O.Order_ID
    LEFT JOIN Books B ON B.Book_ID = OD.Book_ID
    $where_sql
    GROUP BY P.Payment_ID, P.Order_ID, P.Payment_Date, P.Amount, P.Payment_Method, 
             P.Payment_Status, P.Transaction_Ref, O.Order_Date, O.Status,
             U.User_ID, U.Name, U.Email, U.Phone
    ORDER BY P.Payment_Date DESC
";

$stmt = $conn->prepare($query);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$payments_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Payments - Admin Panel</title>
<link rel="stylesheet" href="style.css">
<style>
    .admin-nav { background: #0F172A; }
    .admin-nav a { color: #94A3B8; }
    .admin-nav a:hover, .admin-nav a.active { color: #60A5FA; }

    .stats-bar {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .stat-box .val { font-size: 1.75rem; font-weight: 800; color: var(--primary-color); }
    .stat-box .lbl { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }

    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .tab-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .tab-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-muted);
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .tab-btn:hover, .tab-btn.active {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
    }
    .tab-badge {
        background: rgba(0,0,0,0.12);
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        font-size: 0.75rem;
    }
    .tab-btn.active .tab-badge {
        background: rgba(255,255,255,0.25);
    }

    .search-box {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .search-box input {
        padding: 0.5rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        font-size: 0.85rem;
        width: 240px;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-success { background: #DCFCE7; color: #166534; border: 1px solid #86EFAC; }
    .badge-pending { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
    .badge-failed  { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }

    .method-badge {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .method-upi        { background: #E0E7FF; color: #3730A3; }
    .method-card       { background: #FCE7F3; color: #9D174D; }
    .method-netbanking { background: #E0F2FE; color: #075985; }
    .method-cod        { background: #FEF9C3; color: #854D0E; }

    /* Action Buttons */
    .btn-verify {
        background: #16A34A;
        color: white;
        border: none;
        padding: 0.45rem 0.85rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: background 0.2s ease;
        white-space: nowrap;
    }
    .btn-verify:hover { background: #15803D; }

    .btn-fail {
        background: transparent;
        color: #DC2626;
        border: 1px solid #FCA5A5;
        padding: 0.35rem 0.7rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.78rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-fail:hover { background: #FEE2E2; }

    .btn-reset {
        background: #F1F5F9;
        color: #475569;
        border: 1px solid #CBD5E1;
        padding: 0.35rem 0.7rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .btn-reset:hover { background: #E2E8F0; }

    .ref-input {
        padding: 0.35rem 0.55rem;
        font-size: 0.78rem;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        width: 140px;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        max-width: 440px;
        width: 90%;
        box-shadow: var(--shadow-lg);
    }
</style>
</head>
<body>

<header class="admin-nav">
    <h1 style="color:#F1F5F9;">🛠️ Admin Panel</h1>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="add_book.php">Add Book</a>
        <a href="manage_books.php">Books</a>
        <a href="view_orders.php">Orders</a>
        <a href="manage_payments.php" class="active" style="color:#60A5FA;">Payments <?php if ($stats['pending_txns'] > 0) echo "<span style='background:#F59E0B;color:white;padding:0.1rem 0.45rem;border-radius:999px;font-size:0.75rem;font-weight:700;'>{$stats['pending_txns']}</span>"; ?></a>
        <a href="view_users.php">Users</a>
        <a href="index.php">← Store</a>
        <a href="logout.php" style="color:#F87171;">Logout</a>
    </nav>
</header>

<div class="container" style="max-width: 1200px;">

    <!-- Page Title -->
    <div class="section-title" style="margin-bottom: 1.5rem;">
        <div>
            <span>💳 Customer Payment Management</span>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal; margin-top: 0.25rem;">
                Review and verify payments completed by customers for their orders.
            </div>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stat-box">
            <div class="val"><?php echo number_format($stats['total_txns']); ?></div>
            <div class="lbl">Total Transactions</div>
        </div>
        <div class="stat-box" style="border-color: #FCD34D; background: #FFFDF5;">
            <div class="val" style="color: #D97706;"><?php echo number_format($stats['pending_txns']); ?></div>
            <div class="lbl">⏳ Pending Verification</div>
        </div>
        <div class="stat-box" style="border-color: #86EFAC; background: #F6FEF9;">
            <div class="val" style="color: #16A34A;"><?php echo number_format($stats['success_txns']); ?></div>
            <div class="lbl">✅ Payments Completed</div>
        </div>
        <div class="stat-box" style="border-color: #FCA5A5;">
            <div class="val" style="color: #DC2626;"><?php echo number_format($stats['failed_txns']); ?></div>
            <div class="lbl">❌ Failed Payments</div>
        </div>
        <div class="stat-box" style="border-color: #93C5FD;">
            <div class="val" style="color: var(--accent-color);">₹<?php echo number_format($stats['total_revenue'], 2); ?></div>
            <div class="lbl">Total Revenue Verified</div>
        </div>
    </div>

    <!-- Alert Message -->
    <?php if ($message): ?>
        <div class="<?php echo $msg_type; ?>" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border-radius: 8px;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="tab-group">
            <a href="manage_payments.php?filter=all<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
               class="tab-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">
                All Payments <span class="tab-badge"><?php echo $stats['total_txns']; ?></span>
            </a>
            <a href="manage_payments.php?filter=pending<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
               class="tab-btn <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                ⏳ Pending Verification <span class="tab-badge"><?php echo $stats['pending_txns']; ?></span>
            </a>
            <a href="manage_payments.php?filter=success<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
               class="tab-btn <?php echo $filter === 'success' ? 'active' : ''; ?>">
                ✅ Completed / Verified <span class="tab-badge"><?php echo $stats['success_txns']; ?></span>
            </a>
            <a href="manage_payments.php?filter=failed<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
               class="tab-btn <?php echo $filter === 'failed' ? 'active' : ''; ?>">
                ❌ Failed <span class="tab-badge"><?php echo $stats['failed_txns']; ?></span>
            </a>
        </div>

        <form method="GET" action="manage_payments.php" class="search-box">
            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            <input type="text" name="search" placeholder="Search customer, order #, ref..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn" style="padding: 0.5rem 0.9rem; font-size: 0.85rem;">Search</button>
            <?php if ($search): ?>
                <a href="manage_payments.php?filter=<?php echo $filter; ?>" class="btn btn-outline" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Payments Table -->
    <?php if ($payments_result->num_rows === 0): ?>
        <div style="text-align: center; padding: 4rem 1rem; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px;">
            <div style="font-size: 3rem; margin-bottom: 0.75rem;">💳</div>
            <h3 style="color: var(--text-muted); margin-bottom: 0.5rem;">No payment records found.</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">When customers place orders, their payment transactions will appear here for admin management and verification.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: var(--shadow-sm);">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Txn # / Date</th>
                        <th>Order #</th>
                        <th>Customer Details</th>
                        <th>Ordered Books</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Transaction Ref</th>
                        <th>Payment Status</th>
                        <th>Admin Verification & Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $payments_result->fetch_assoc()): 
                    $p_status = $row['Payment_Status'];
                    $o_status = $row['Order_Status'];
                    $method   = $row['Payment_Method'];
                    $method_class = 'method-' . strtolower($method);
                ?>
                    <tr>
                        <!-- Txn # & Date -->
                        <td>
                            <strong>#TXN-<?php echo $row['Payment_ID']; ?></strong>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">
                                <?php echo date('M d, Y', strtotime($row['Payment_Date'])); ?>
                                <span style="font-size: 0.72rem;"><?php echo date('g:i A', strtotime($row['Payment_Date'])); ?></span>
                            </div>
                        </td>

                        <!-- Order ID & Order Status -->
                        <td>
                            <a href="view_orders.php?filter=all" style="font-weight: 700; color: var(--accent-color); text-decoration: none;">
                                #ORD-<?php echo $row['Order_ID']; ?>
                            </a>
                            <div style="margin-top: 0.25rem;">
                                <span style="font-size: 0.72rem; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 600;
                                    background: <?php 
                                        if ($o_status === 'Confirmed') echo '#DBEAFE; color:#1E40AF';
                                        elseif ($o_status === 'Delivered') echo '#DCFCE7; color:#166534';
                                        elseif ($o_status === 'Cancelled') echo '#FEE2E2; color:#991B1B';
                                        else echo '#FEF3C7; color:#92400E';
                                    ?>;">
                                    <?php echo $o_status; ?>
                                </span>
                            </div>
                        </td>

                        <!-- Customer Details -->
                        <td>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($row['Customer_Name']); ?></div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);"><?php echo htmlspecialchars($row['Customer_Email']); ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">📞 <?php echo htmlspecialchars($row['Customer_Phone']); ?></div>
                        </td>

                        <!-- Books -->
                        <td style="max-width: 200px; font-size: 0.82rem; color: var(--text-muted);">
                            <?php echo htmlspecialchars($row['Items_Summary'] ?: 'No details'); ?>
                        </td>

                        <!-- Amount -->
                        <td style="white-space: nowrap; font-weight: 800; font-size: 1.05rem; color: var(--primary-color);">
                            ₹<?php echo number_format($row['Amount'], 2); ?>
                        </td>

                        <!-- Method -->
                        <td>
                            <span class="method-badge <?php echo $method_class; ?>">
                                <?php 
                                    if ($method === 'UPI') echo '📱 ' . $method;
                                    elseif ($method === 'Card') echo '💳 ' . $method;
                                    elseif ($method === 'NetBanking') echo '🏦 ' . $method;
                                    else echo '💵 ' . $method;
                                ?>
                            </span>
                        </td>

                        <!-- Transaction Ref / Note -->
                        <td>
                            <?php if ($row['Transaction_Ref']): ?>
                                <code style="font-size: 0.78rem; background: #F1F5F9; padding: 0.2rem 0.4rem; border-radius: 4px;">
                                    <?php echo htmlspecialchars($row['Transaction_Ref']); ?>
                                </code>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.78rem; font-style: italic;">None</span>
                            <?php endif; ?>
                        </td>

                        <!-- Payment Status Badge -->
                        <td>
                            <?php if ($p_status === 'Success'): ?>
                                <span class="badge badge-success">✅ Completed</span>
                            <?php elseif ($p_status === 'Failed'): ?>
                                <span class="badge badge-failed">❌ Failed</span>
                            <?php else: ?>
                                <span class="badge badge-pending">⏳ Pending</span>
                            <?php endif; ?>
                        </td>

                        <!-- Admin Management Actions -->
                        <td style="white-space: nowrap;">
                            <?php if ($p_status !== 'Success'): ?>
                                <form method="POST" action="manage_payments.php?filter=<?php echo urlencode($filter); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" style="display: flex; flex-direction: column; gap: 0.4rem;">
                                    <input type="hidden" name="action" value="complete_payment">
                                    <input type="hidden" name="payment_id" value="<?php echo $row['Payment_ID']; ?>">
                                    <div style="display: flex; gap: 0.3rem;">
                                        <input type="text" name="transaction_ref" placeholder="Ref/Note (optional)" class="ref-input" value="<?php echo htmlspecialchars($row['Transaction_Ref'] ?? ''); ?>">
                                        <button type="submit" class="btn-verify" title="Verify and mark payment as completed">
                                            ✅ Verify & Complete
                                        </button>
                                    </div>
                                </form>
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.35rem; align-items: center;">
                                    <form method="POST" action="manage_payments.php?filter=<?php echo urlencode($filter); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" onsubmit="return confirm('Mark this transaction as FAILED?');" style="display:inline;">
                                        <input type="hidden" name="action" value="fail_payment">
                                        <input type="hidden" name="payment_id" value="<?php echo $row['Payment_ID']; ?>">
                                        <input type="hidden" name="cancel_order" value="1">
                                        <button type="submit" class="btn-fail">❌ Mark Failed</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 0.8rem; color: #16A34A; font-weight: 700;">✅ Paid & Verified</span>
                                    <form method="POST" action="manage_payments.php?filter=<?php echo urlencode($filter); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" onsubmit="return confirm('Reset payment status to Pending?');" style="display:inline;">
                                        <input type="hidden" name="action" value="reset_payment">
                                        <input type="hidden" name="payment_id" value="<?php echo $row['Payment_ID']; ?>">
                                        <button type="submit" class="btn-reset" title="Reset payment status back to pending">🔄 Reset</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
