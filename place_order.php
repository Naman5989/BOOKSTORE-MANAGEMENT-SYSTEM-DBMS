<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    die("Your cart is empty.");
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'COD';
$total = 0;

$conn->begin_transaction();
try {
    // Create the order with 0 total first
    $stmt = $conn->prepare("INSERT INTO Orders (User_ID, Total_Amount, Status) VALUES (?, 0, 'Pending')");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Add order line items
    $stmt_fetch = $conn->prepare("SELECT Price, Stock FROM Books WHERE Book_ID = ?");
    $stmt_insert = $conn->prepare("INSERT INTO Order_Details (Order_ID, Book_ID, Quantity, Price) VALUES (?, ?, ?, ?)");
    
    foreach ($cart as $book_id => $quantity) {
        $stmt_fetch->bind_param("i", $book_id);
        $stmt_fetch->execute();
        $book = $stmt_fetch->get_result()->fetch_assoc();
        
        if (!$book) {
            throw new Exception("Book ID $book_id not found.");
        }
        if ($quantity > $book['Stock']) {
            throw new Exception("Requested quantity exceeds available stock for Book ID $book_id.");
        }
        
        $price = $book['Price'];
        $line_total = $price * $quantity;
        $total += $line_total;
        
        $stmt_insert->bind_param("iiid", $order_id, $book_id, $quantity, $price);
        $stmt_insert->execute();
    }

    // Update total amount
    $stmt_update = $conn->prepare("UPDATE Orders SET Total_Amount = ? WHERE Order_ID = ?");
    $stmt_update->bind_param("di", $total, $order_id);
    $stmt_update->execute();

    // Record a pending payment
    $stmt_pay = $conn->prepare("INSERT INTO Payment (Order_ID, Amount, Payment_Method, Payment_Status) VALUES (?, ?, ?, 'Pending')");
    $stmt_pay->bind_param("ids", $order_id, $total, $payment_method);
    $stmt_pay->execute();

    $conn->commit();
    unset($_SESSION['cart']); // clear cart
    
    header("Location: orders.php?placed=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Order failed: " . $e->getMessage());
}
?>
