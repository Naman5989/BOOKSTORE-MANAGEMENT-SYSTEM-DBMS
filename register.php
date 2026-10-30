<?php
require_once 'db_connect.php';
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']   ?? '';
    $phone = trim($_POST['phone'] ?? '');

    // ---- Validation ----
    if ($name === '') $errors[] = "Name cannot be empty.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if (strlen($pass) < 6) $errors[] = "Password must be at least 6 characters.";
    if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "Phone number must be exactly 10 digits.";

    if (empty($errors)) {
        $check = $conn->prepare("SELECT User_ID FROM Users WHERE Email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = "An account with this email already exists.";
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO Users (Name, Email, Password, Phone, Role) VALUES (?, ?, ?, ?, 'customer')"
            );
            $stmt->bind_param("ssss", $name, $email, $hashed, $phone);
            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Online Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📚 Online Bookstore</h1>
    <nav><a href="index.php">Home</a><a href="login.php">Login</a></nav>
</header>

<div class="container">
    <h2>Create an Account</h2>

    <?php foreach ($errors as $e): ?>
        <div class="error"><?php echo htmlspecialchars($e); ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?> <a href="login.php">Login here</a>.</div>
    <?php endif; ?>

    <form method="post" action="register.php">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label>Password</label>
        <input type="password" name="password">

        <label>Phone (10 digits)</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">

        <button type="submit">Register</button>
    </form>
</div>

</body>
</html>
