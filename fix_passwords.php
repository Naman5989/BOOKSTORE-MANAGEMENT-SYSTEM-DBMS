<?php
require_once 'db_connect.php';

// Update admin
$hash_admin = password_hash('admin123', PASSWORD_DEFAULT);
$conn->query("UPDATE Users SET Password = '$hash_admin' WHERE Email = 'admin@bookstore.com'");

// Update demo customers
$hash_naman = password_hash('pass123', PASSWORD_DEFAULT);
$conn->query("UPDATE Users SET Password = '$hash_naman' WHERE Email = 'naman@example.com'");

$hash_riya = password_hash('pass456', PASSWORD_DEFAULT);
$conn->query("UPDATE Users SET Password = '$hash_riya' WHERE Email = 'riya@example.com'");

echo "<h2>✅ All passwords updated successfully!</h2>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Email</th><th>Password</th></tr>";
echo "<tr><td>admin@bookstore.com</td><td>admin123</td></tr>";
echo "<tr><td>naman@example.com</td><td>pass123</td></tr>";
echo "<tr><td>riya@example.com</td><td>pass456</td></tr>";
echo "</table>";
echo "<br><a href='login.php'>Go to Login →</a>";
?>
