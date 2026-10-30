<?php
require_once __DIR__ . '/../frontend/db_connect.php';

// Check if there are already orders
$count = $conn->query("SELECT COUNT(*) AS c FROM Orders")->fetch_assoc()['c'];
if ($count > 0) {
    echo "Orders already exist ($count orders).\n";
    exit;
}

echo "Seeding demo orders with payments...\n";

// Order 1: Naman Sharma - UPI (Pending)
$user_id = 2; // Naman
$conn->query("INSERT INTO Orders (User_ID, Order_Date, Total_Amount, Status) VALUES ($user_id, NOW() - INTERVAL 2 HOUR, 798.00, 'Pending')");
$o1 = $conn->insert_id;
// Book 1984 (around ID 4) and Atomic Habits
$b1 = $conn->query("SELECT Book_ID, Price FROM Books WHERE ISBN = '9780451524935'")->fetch_assoc();
$b2 = $conn->query("SELECT Book_ID, Price FROM Books WHERE ISBN = '9780735211292'")->fetch_assoc();
if ($b1) $conn->query("INSERT INTO Order_Details (Order_ID, Book_ID, Quantity, Price) VALUES ($o1, {$b1['Book_ID']}, 1, {$b1['Price']})");
if ($b2) $conn->query("INSERT INTO Order_Details (Order_ID, Book_ID, Quantity, Price) VALUES ($o1, {$b2['Book_ID']}, 1, {$b2['Price']})");
$conn->query("INSERT INTO Payment (Order_ID, Payment_Date, Amount, Payment_Method, Payment_Status, Transaction_Ref) VALUES ($o1, NOW() - INTERVAL 2 HOUR, 798.00, 'UPI', 'Pending', 'UPI-9876543210@paytm')");

// Order 2: Riya Verma - Card (Pending)
$user_id = 3; // Riya
$conn->query("INSERT INTO Orders (User_ID, Order_Date, Total_Amount, Status) VALUES ($user_id, NOW() - INTERVAL 1 HOUR, 670.00, 'Pending')");
$o2 = $conn->insert_id;
$b3 = $conn->query("SELECT Book_ID, Price FROM Books WHERE ISBN = '9780547928227'")->fetch_assoc(); // The Hobbit
$b4 = $conn->query("SELECT Book_ID, Price FROM Books WHERE ISBN = '9780141439518'")->fetch_assoc(); // Pride and Prejudice
if ($b3) $conn->query("INSERT INTO Order_Details (Order_ID, Book_ID, Quantity, Price) VALUES ($o2, {$b3['Book_ID']}, 1, {$b3['Price']})");
if ($b4) $conn->query("INSERT INTO Order_Details (Order_ID, Book_ID, Quantity, Price) VALUES ($o2, {$b4['Book_ID']}, 1, {$b4['Price']})");
$conn->query("INSERT INTO Payment (Order_ID, Payment_Date, Amount, Payment_Method, Payment_Status, Transaction_Ref) VALUES ($o2, NOW() - INTERVAL 1 HOUR, 670.00, 'Card', 'Pending', 'AUTH-CARD-4421')");

// Order 3: Naman Sharma - NetBanking (Success / Confirmed)
$conn->query("INSERT INTO Orders (User_ID, Order_Date, Total_Amount, Status) VALUES (2, NOW() - INTERVAL 1 DAY, 199.00, 'Confirmed')");
$o3 = $conn->insert_id;
$b5 = $conn->query("SELECT Book_ID, Price FROM Books WHERE Book_ID = 2")->fetch_assoc(); // Five Point Someone
if ($b5) $conn->query("INSERT INTO Order_Details (Order_ID, Book_ID, Quantity, Price) VALUES ($o3, {$b5['Book_ID']}, 1, {$b5['Price']})");
$conn->query("INSERT INTO Payment (Order_ID, Payment_Date, Amount, Payment_Method, Payment_Status, Transaction_Ref) VALUES ($o3, NOW() - INTERVAL 1 DAY, 199.00, 'NetBanking', 'Success', 'HDFC-TXN-881239')");

// Order 4: Riya Verma - COD (Pending)
$conn->query("INSERT INTO Orders (User_ID, Order_Date, Total_Amount, Status) VALUES (3, NOW() - INTERVAL 30 MINUTE, 299.00, 'Pending')");
$o4 = $conn->insert_id;
$b6 = $conn->query("SELECT Book_ID, Price FROM Books WHERE ISBN = '9780062315007'")->fetch_assoc(); // The Alchemist
if ($b6) $conn->query("INSERT INTO Order_Details (Order_ID, Book_ID, Quantity, Price) VALUES ($o4, {$b6['Book_ID']}, 1, {$b6['Price']})");
$conn->query("INSERT INTO Payment (Order_ID, Payment_Date, Amount, Payment_Method, Payment_Status, Transaction_Ref) VALUES ($o4, NOW() - INTERVAL 30 MINUTE, 299.00, 'COD', 'Pending', NULL)");

echo "✅ Created demo orders with payments successfully!\n";
