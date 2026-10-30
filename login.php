<?php
require_once 'db_connect.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = "Both email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT User_ID, Name, Password, Role FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $authenticated = password_verify($pass, $user['Password']);
            if (!$authenticated && $pass === $user['Password']) {
                $authenticated = true;
                $newHash = password_hash($pass, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE Users SET Password = ? WHERE User_ID = ?");
                $updateStmt->bind_param("si", $newHash, $user['User_ID']);
                $updateStmt->execute();
            }
            if ($authenticated) {
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['name']    = $user['Name'];
                $_SESSION['role']    = $user['Role'];
                header("Location: " . ($user['Role'] === 'admin' ? "admin.php" : "index.php"));
                exit;
            } else {
                $error = "Incorrect email or password.";
            }
        } else {
            $error = "Incorrect email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Online Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📚 Online Bookstore</h1>
    <nav><a href="index.php">Home</a><a href="register.php">Register</a></nav>
</header>

<div class="container">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label>Password</label>
        <input type="password" name="password">

        <button type="submit">Login</button>
    </form>

    <p style="margin-top:16px; font-size:14px;">
        Demo admin: admin@bookstore.com / admin123 (after seeding + hashing password)
    </p>
</div>

</body>
</html>
