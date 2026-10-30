<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = "";

// Fetch authors & categories for dropdowns
$authors_list   = $conn->query("SELECT * FROM Authors ORDER BY Author_Name");
$categories_list = $conn->query("SELECT * FROM Categories ORDER BY Category_Name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $isbn        = trim($_POST['isbn'] ?? '');
    $author      = trim($_POST['author'] ?? '');
    $new_author  = trim($_POST['new_author'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $new_cat     = trim($_POST['new_category'] ?? '');
    $price       = $_POST['price'] ?? '';
    $stock       = $_POST['stock'] ?? '';
    $cover_url   = trim($_POST['cover_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '')  $errors[] = "Title cannot be empty.";
    if ($isbn === '')   $errors[] = "ISBN cannot be empty.";
    if (!is_numeric($price) || $price < 0) $errors[] = "Price must be a non-negative number.";
    if (!is_numeric($stock) || intval($stock) < 0) $errors[] = "Stock must be a non-negative whole number.";
    if ($new_author === '' && intval($author) <= 0) $errors[] = "Please select an existing author or type a new one.";
    if ($new_cat === '' && intval($category) <= 0)  $errors[] = "Please select an existing category or type a new one.";

    if (empty($errors)) {
        // Resolve Author
        if ($new_author !== '') {
            $chk = $conn->prepare("SELECT Author_ID FROM Authors WHERE Author_Name = ?");
            $chk->bind_param("s", $new_author);
            $chk->execute();
            $chk_res = $chk->get_result();
            if ($chk_res->num_rows > 0) {
                $author_id = $chk_res->fetch_assoc()['Author_ID'];
            } else {
                $ins = $conn->prepare("INSERT INTO Authors (Author_Name) VALUES (?)");
                $ins->bind_param("s", $new_author);
                $ins->execute();
                $author_id = $conn->insert_id;
            }
        } else {
            $author_id = intval($author);
        }

        // Resolve Category
        if ($new_cat !== '') {
            $chk = $conn->prepare("SELECT Category_ID FROM Categories WHERE Category_Name = ?");
            $chk->bind_param("s", $new_cat);
            $chk->execute();
            $chk_res = $chk->get_result();
            if ($chk_res->num_rows > 0) {
                $category_id = $chk_res->fetch_assoc()['Category_ID'];
            } else {
                $ins = $conn->prepare("INSERT INTO Categories (Category_Name) VALUES (?)");
                $ins->bind_param("s", $new_cat);
                $ins->execute();
                $category_id = $conn->insert_id;
            }
        } else {
            $category_id = intval($category);
        }

        if ($cover_url === '') {
            $cover_url = "https://placehold.co/400x600/2c3e50/ffffff?text=" . urlencode($title);
        }

        $price_f = floatval($price);
        $stock_i = intval($stock);

        // Correct bind_param types: s=Title, s=ISBN, i=Author_ID, i=Category_ID, d=Price, i=Stock, s=Cover, s=Desc
        $stmt = $conn->prepare(
            "INSERT INTO Books (Title, ISBN, Author_ID, Category_ID, Price, Stock, Cover_Image_URL, Description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssiidiss", $title, $isbn, $author_id, $category_id, $price_f, $stock_i, $cover_url, $description);

        if ($stmt->execute()) {
            $success = "✅ Book \"" . htmlspecialchars($title) . "\" added successfully!";
        } else {
            $errors[] = "Could not add book: " . $conn->error . " (ISBN may already exist).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Book - Admin</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header style="background:#0F172A;">
    <h1 style="color:#F1F5F9;">🛠️ Admin Panel</h1>
    <nav>
        <a href="admin.php" style="color:#94A3B8;">Dashboard</a>
        <a href="add_book.php" style="color:#60A5FA;" class="active">Add Book</a>
        <a href="manage_books.php" style="color:#94A3B8;">Books</a>
        <a href="view_orders.php" style="color:#94A3B8;">Orders</a>
        <a href="manage_payments.php" style="color:#94A3B8;">Payments</a>
        <a href="view_users.php" style="color:#94A3B8;">Users</a>
        <a href="index.php" style="color:#94A3B8;">← Store</a>
        <a href="logout.php" style="color:#F87171;">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="section-title"><span>Add New Book</span></div>

    <?php foreach ($errors as $e): ?>
        <div class="error"><?php echo htmlspecialchars($e); ?></div>
    <?php endforeach; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="form-card" style="max-width: 720px; margin: 0;">
        <form method="POST" action="add_book.php">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Book Title *</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" placeholder="e.g. Harry Potter and the Sorcerer's Stone" required>
                </div>
                <div class="form-group">
                    <label>ISBN *</label>
                    <input type="text" name="isbn" value="<?php echo htmlspecialchars($_POST['isbn'] ?? ''); ?>" placeholder="e.g. 9780747532699" required>
                </div>
                <div class="form-group">
                    <label>Price (₹) *</label>
                    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" placeholder="e.g. 499.00" required>
                </div>
                <div class="form-group">
                    <label>Author (select existing)</label>
                    <select name="author">
                        <option value="">-- Select Author --</option>
                        <?php $authors_list->data_seek(0); while ($a = $authors_list->fetch_assoc()): ?>
                            <option value="<?php echo $a['Author_ID']; ?>"><?php echo htmlspecialchars($a['Author_Name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Or Add New Author</label>
                    <input type="text" name="new_author" placeholder="Type new author name">
                </div>
                <div class="form-group">
                    <label>Category (select existing)</label>
                    <select name="category">
                        <option value="">-- Select Category --</option>
                        <?php $categories_list->data_seek(0); while ($c = $categories_list->fetch_assoc()): ?>
                            <option value="<?php echo $c['Category_ID']; ?>"><?php echo htmlspecialchars($c['Category_Name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Or Add New Category</label>
                    <input type="text" name="new_category" placeholder="Type new category name">
                </div>
                <div class="form-group">
                    <label>Stock *</label>
                    <input type="number" name="stock" value="<?php echo htmlspecialchars($_POST['stock'] ?? ''); ?>" placeholder="e.g. 25" required>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Cover Image URL (optional – leave blank to auto-generate)</label>
                    <input type="text" name="cover_url" value="<?php echo htmlspecialchars($_POST['cover_url'] ?? ''); ?>" placeholder="https://...">
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="A short description of the book..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn" style="width:100%; padding: 0.9rem; font-size: 1.05rem; margin-top: 0.5rem;">➕ Add Book</button>
        </form>
    </div>
</div>

</body>
</html>
