<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
    
    if ($book_id > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$book_id])) {
            $_SESSION['cart'][$book_id]++;
        } else {
            $_SESSION['cart'][$book_id] = 1;
        }
    }
    
    // Redirect back to the referrer or cart
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: " . $referer);
    exit;
}
?>
