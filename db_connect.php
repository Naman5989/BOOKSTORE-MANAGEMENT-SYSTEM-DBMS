<?php
// =====================================================
// db_connect.php
// Central MySQL connection used by every page
// =====================================================

$DB_HOST = "localhost";
$DB_USER = "root";        // change to 'normaluser' / 'admin' as needed
$DB_PASS = "Naman5989";             // set your MySQL root/user password
$DB_NAME = "bookstore";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Start session for login state (used across all pages)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
