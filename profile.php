<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Handle Update Profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']);
    $new_password = $_POST['new_password'];
    
    if (preg_match('/^[0-9]{10}$/', $phone)) {
        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Users SET Phone = ?, Password = ? WHERE User_ID = ?");
            $stmt->bind_param("ssi", $phone, $hashed, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE Users SET Phone = ? WHERE User_ID = ?");
            $stmt->bind_param("si", $phone, $user_id);
        }
        
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
    } else {
        $error = "Invalid phone number format. Must be 10 digits.";
    }
}

// Fetch User Info
$stmt = $conn->prepare("SELECT Name, Email, Phone, Role, Created_At FROM Users WHERE User_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch Orders
$stmt_orders = $conn->prepare("
    SELECT O.Order_ID, O.Order_Date, O.Total_Amount, O.Status,
           GROUP_CONCAT(B.Title ORDER BY B.Title SEPARATOR ', ') AS Books
    FROM Orders O
    JOIN Order_Details OD ON O.Order_ID = OD.Order_ID
    JOIN Books B ON OD.Book_ID = B.Book_ID
    WHERE O.User_ID = ?
    GROUP BY O.Order_ID, O.Order_Date, O.Total_Amount, O.Status
    ORDER BY O.Order_Date DESC
");
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile - Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📚 Bookstore</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="books.php">Browse</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin.php">Admin</a>
        <?php else: ?>
            <a href="profile.php" class="active">Profile</a>
        <?php endif; ?>
        <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['name']); ?>)</a>
        <a href="cart.php">Cart <span class="cart-count"><?php echo $cart_count; ?></span></a>
    </nav>
</header>

<div class="container layout-sidebar">
    <aside class="sidebar">
        <h3>Account Details</h3>
        <p style="margin-bottom: 0.5rem;"><strong>Name:</strong> <br><?php echo htmlspecialchars($user['Name']); ?></p>
        <p style="margin-bottom: 0.5rem;"><strong>Email:</strong> <br><?php echo htmlspecialchars($user['Email']); ?></p>
        <p style="margin-bottom: 0.5rem;"><strong>Member Since:</strong> <br><?php echo date('M Y', strtotime($user['Created_At'])); ?></p>
        
        <hr style="margin: 1.5rem 0; border: none; border-bottom: 1px solid var(--border-color);">
        
        <h3>Update Profile</h3>
        <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        
        <form method="POST" action="profile.php">
            <input type="hidden" name="update_profile" value="1">
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['Phone']); ?>" required pattern="[0-9]{10}">
            </div>
            <div class="form-group">
                <label>New Password (Optional)</label>
                <input type="password" name="new_password" placeholder="Leave blank to keep current">
            </div>
            <button type="submit" class="btn" style="width: 100%;">Save Changes</button>
        </form>
    </aside>

    <main>
        <div class="section-title">
            <span>Order History</span>
        </div>

        <?php if ($orders_result->num_rows > 0): ?>
            <table class="data-table">
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = $orders_result->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $row['Order_ID']; ?></strong></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($row['Order_Date'])); ?></td>
                        <td><span style="font-size: 0.9rem; color: var(--text-muted);"><?php echo htmlspecialchars($row['Books']); ?></span></td>
                        <td style="font-weight: 600;">₹<?php echo number_format($row['Total_Amount'], 2); ?></td>
                        <td>
                            <?php 
                            $badge_class = $row['Status'] === 'Pending' ? 'badge-pending' : 'badge-success';
                            echo "<span class='badge {$badge_class}'>" . htmlspecialchars($row['Status']) . "</span>"; 
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 0; background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color);">
                <h3 style="color: var(--text-muted); margin-bottom: 1rem;">You haven't placed any orders yet.</h3>
                <a href="books.php" class="btn">Start Shopping</a>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
