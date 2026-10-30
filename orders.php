<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// Fixed query — add P.Payment_Method to GROUP BY to satisfy only_full_group_by
$stmt = $conn->prepare("
    SELECT O.Order_ID, O.Order_Date, O.Total_Amount, O.Status,
           MAX(P.Payment_Method) AS Payment_Method,
           MAX(P.Payment_Status) AS Payment_Status,
           MAX(P.Transaction_Ref) AS Transaction_Ref,
           GROUP_CONCAT(B.Title ORDER BY B.Title SEPARATOR ', ') AS Books
    FROM Orders O
    JOIN Order_Details OD ON O.Order_ID = OD.Order_ID
    JOIN Books B ON OD.Book_ID = B.Book_ID
    LEFT JOIN Payment P ON P.Order_ID = O.Order_ID
    WHERE O.User_ID = ?
    GROUP BY O.Order_ID, O.Order_Date, O.Total_Amount, O.Status
    ORDER BY O.Order_Date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$status_styles = [
    'Pending'   => ['bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'⏳'],
    'Confirmed' => ['bg'=>'#DBEAFE','color'=>'#1E40AF','icon'=>'✅'],
    'Shipped'   => ['bg'=>'#EDE9FE','color'=>'#5B21B6','icon'=>'🚚'],
    'Delivered' => ['bg'=>'#DCFCE7','color'=>'#166534','icon'=>'📦'],
    'Cancelled' => ['bg'=>'#FEE2E2','color'=>'#991B1B','icon'=>'❌'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📚 Bookstore</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="books.php">Browse</a>
        <a href="profile.php" class="active">Profile</a>
        <a href="logout.php">Logout</a>
        <a href="cart.php">Cart <span class="cart-count"><?php echo $cart_count; ?></span></a>
    </nav>
</header>

<div class="container">
    <?php if (isset($_GET['placed'])): ?>
        <div style="background: #F0FDF4; border: 1px solid #4ADE80; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; text-align: center;">
            <div style="font-size: 3rem;">✅</div>
            <h2 style="color: #166534; margin: 0.5rem 0;">Order Placed Successfully!</h2>
            <p style="color: #4B7C5E;">Your payment transaction has been initiated. The admin will review and confirm your order shortly.</p>
        </div>
    <?php endif; ?>

    <div class="section-title"><span>My Orders</span></div>

    <?php if ($result->num_rows === 0): ?>
        <div style="text-align:center; padding: 4rem 0; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
            <h3 style="color: var(--text-muted); margin-bottom: 1rem;">You haven't placed any orders yet.</h3>
            <a href="books.php" class="btn">Start Shopping</a>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 1.25rem;">
        <?php while ($row = $result->fetch_assoc()):
            $status = $row['Status'];
            $style  = $status_styles[$status] ?? ['bg'=>'#F1F5F9','color'=>'#475569','icon'=>'📋'];
            $pay_status = $row['Payment_Status'] ?? 'Pending';
        ?>
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.75rem; box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin:0; font-size: 1.1rem;">Order #<?php echo $row['Order_ID']; ?></h3>
                        <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;"><?php echo date('F j, Y — g:i A', strtotime($row['Order_Date'])); ?></p>
                    </div>
                    <span style="padding: 0.4rem 1rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700; background: <?php echo $style['bg']; ?>; color: <?php echo $style['color']; ?>;">
                        <?php echo $style['icon']; ?> <?php echo $status; ?>
                    </span>
                </div>

                <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: center;">
                    <div>
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0 0 0.4rem;"><strong style="color: var(--primary-color);">Books:</strong> <?php echo htmlspecialchars($row['Books']); ?></p>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                            <span style="font-size: 0.85rem; color: var(--text-muted);">
                                💳 <strong><?php echo htmlspecialchars($row['Payment_Method'] ?? 'N/A'); ?></strong>
                            </span>
                            <span style="font-size: 0.85rem; padding: 0.2rem 0.75rem; border-radius: 999px; font-weight: 700;
                                background: <?php echo $pay_status === 'Success' ? '#DCFCE7' : ($pay_status === 'Failed' ? '#FEE2E2' : '#FEF3C7'); ?>;
                                color: <?php echo $pay_status === 'Success' ? '#166534' : ($pay_status === 'Failed' ? '#991B1B' : '#92400E'); ?>;">
                                <?php 
                                    if ($pay_status === 'Success') echo '✅ Payment Verified & Completed by Admin';
                                    elseif ($pay_status === 'Failed') echo '❌ Payment Failed / Rejected';
                                    else echo '⏳ Payment Pending Admin Verification';
                                ?>
                            </span>
                            <?php if (!empty($row['Transaction_Ref'])): ?>
                                <span style="font-size: 0.8rem; color: var(--text-muted); background: #F1F5F9; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                    Ref: <?php echo htmlspecialchars($row['Transaction_Ref']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--accent-color);">₹<?php echo number_format($row['Total_Amount'], 2); ?></div>
                    </div>
                </div>

                <?php if ($status === 'Pending'): ?>
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 8px; font-size: 0.875rem; color: #92400E;">
                    ⏳ Your order and payment have been submitted! The admin is reviewing and verifying the payment. Once approved, your order will be confirmed.
                </div>
                <?php elseif ($status === 'Confirmed'): ?>
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #EFF6FF; border: 1px solid #93C5FD; border-radius: 8px; font-size: 0.875rem; color: #1D4ED8;">
                    ✅ Your order has been confirmed! It will be shipped soon.
                </div>
                <?php elseif ($status === 'Shipped'): ?>
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #F5F3FF; border: 1px solid #C4B5FD; border-radius: 8px; font-size: 0.875rem; color: #5B21B6;">
                    🚚 Your order is on its way! Expected delivery in 3-5 working days.
                </div>
                <?php elseif ($status === 'Delivered'): ?>
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #F0FDF4; border: 1px solid #4ADE80; border-radius: 8px; font-size: 0.875rem; color: #166534;">
                    📦 Your order has been delivered. Enjoy your books! Don't forget to leave a review.
                </div>
                <?php elseif ($status === 'Cancelled'): ?>
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 8px; font-size: 0.875rem; color: #991B1B;">
                    ❌ This order was cancelled. If you have questions, please contact support.
                </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
