<?php
require_once 'db_connect.php';

// Handle remove item
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$cart_count = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Shopping Cart - Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📚 Bookstore</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="books.php">Browse</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php">Admin</a>
            <?php else: ?>
                <a href="profile.php">Profile</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
        <a href="cart.php" class="active">Cart <span class="cart-count"><?php echo $cart_count; ?></span></a>
    </nav>
</header>

<div class="container">
    <div class="section-title">
        <span>Your Shopping Cart</span>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
        <div style="text-align:center; padding: 4rem 0;">
            <h3 style="color:var(--text-muted); margin-bottom: 1rem;">Your cart is empty</h3>
            <a href="books.php" class="btn">Browse Books</a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <tr>
                <th>Book</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php
            $total = 0;
            $ids = implode(',', array_keys($_SESSION['cart']));
            $result = $conn->query("SELECT Book_ID, Title, Price FROM Books WHERE Book_ID IN ($ids)");
            
            while ($row = $result->fetch_assoc()) {
                $qty = $_SESSION['cart'][$row['Book_ID']];
                $subtotal = $row['Price'] * $qty;
                $total += $subtotal;
                echo "<tr>
                        <td>" . htmlspecialchars($row['Title']) . "</td>
                        <td>₹" . number_format($row['Price'], 2) . "</td>
                        <td>{$qty}</td>
                        <td>₹" . number_format($subtotal, 2) . "</td>
                        <td><a href='cart.php?remove={$row['Book_ID']}' style='color:red;'>Remove</a></td>
                      </tr>";
            }
            ?>
            <tr>
                <td colspan="3" style="text-align:right; font-weight:700;">Total:</td>
                <td colspan="2" style="font-weight:700; font-size:1.2rem; color:var(--primary-color);">₹<?php echo number_format($total, 2); ?></td>
            </tr>
        </table>
        
        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="place_order.php" method="POST" style="display: flex; gap: 1rem; align-items: flex-end;">
                    <div style="text-align: left;">
                        <label for="payment_method" style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--primary-color);">Payment Method</label>
                        <select name="payment_method" id="payment_method" style="padding: 0.8rem; border-radius: 6px; border: 1px solid var(--border-color); font-size: 1rem;">
                            <option value="Card">Credit/Debit Card</option>
                            <option value="UPI">UPI</option>
                            <option value="NetBanking">NetBanking</option>
                            <option value="COD">Cash on Delivery</option>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="font-size: 1.1rem; padding: 0.8rem 2rem; height: fit-content;">Proceed to Checkout</button>
                </form>
            <?php else: ?>
                <div style="text-align:right;">
                    <p style="margin-bottom: 1rem; color: var(--text-muted);">Please login to place your order.</p>
                    <a href="login.php" class="btn">Login to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
