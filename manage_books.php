<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message  = "";
$msg_type = "success";

// ---- Handle delete ----
if (isset($_GET['delete'])) {
    $book_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Books WHERE Book_ID = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $message = "Book deleted successfully.";
}

// ---- Handle update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id     = intval($_POST['book_id']);
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];
    $cover_url   = trim($_POST['cover_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!is_numeric($price) || $price < 0) {
        $message  = "Invalid price.";
        $msg_type = "error";
    } elseif (!ctype_digit((string)$stock) || $stock < 0) {
        $message  = "Invalid stock value.";
        $msg_type = "error";
    } else {
        $stmt = $conn->prepare("UPDATE Books SET Price = ?, Stock = ?, Cover_Image_URL = ?, Description = ? WHERE Book_ID = ?");
        $stmt->bind_param("dissi", $price, $stock, $cover_url, $description, $book_id);
        $stmt->execute();
        $message = "Book updated successfully.";
    }
}

// Pagination
$page   = isset($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$limit  = 20;
$offset = ($page - 1) * $limit;
$total_books = $conn->query("SELECT COUNT(*) AS c FROM Books")->fetch_assoc()['c'];
$total_pages = ceil($total_books / $limit);

$result = $conn->query("
    SELECT B.Book_ID, B.Title, A.Author_Name, C.Category_Name, B.Price, B.Stock, B.Cover_Image_URL, B.Description
    FROM Books B
    JOIN Authors A ON B.Author_ID = A.Author_ID
    JOIN Categories C ON B.Category_ID = C.Category_ID
    ORDER BY B.Book_ID DESC
    LIMIT $limit OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Books - Admin</title>
<link rel="stylesheet" href="style.css">
<style>
    .edit-row { background: #F8FAFC; }
    .edit-input { padding: 0.35rem 0.5rem; border: 1px solid var(--border-color); border-radius: 4px; font-size: 0.9rem; width: 100%; }
    .save-btn { background: var(--accent-color); color: white; border: none; padding: 0.4rem 0.9rem; border-radius: 5px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: background 0.2s; }
    .save-btn:hover { background: var(--accent-hover); }
    .del-btn { color: #DC2626; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; }
    .del-btn:hover { text-decoration: underline; }
</style>
</head>
<body>

<header style="background:#0F172A;">
    <h1 style="color:#F1F5F9;">🛠️ Admin Panel</h1>
    <nav>
        <a href="admin.php" style="color:#94A3B8;">Dashboard</a>
        <a href="add_book.php" style="color:#94A3B8;">Add Book</a>
        <a href="manage_books.php" style="color:#60A5FA;" class="active">Books</a>
        <a href="view_orders.php" style="color:#94A3B8;">Orders</a>
        <a href="manage_payments.php" style="color:#94A3B8;">Payments</a>
        <a href="view_users.php" style="color:#94A3B8;">Users</a>
        <a href="index.php" style="color:#94A3B8;">← Store</a>
        <a href="logout.php" style="color:#F87171;">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="section-title">
        <span>Manage Books (<?php echo $total_books; ?> total)</span>
        <a href="add_book.php" class="btn" style="font-size:0.85rem; padding:0.4rem 0.9rem;">➕ Add New</a>
    </div>

    <?php if ($message): ?>
        <div class="<?php echo $msg_type; ?>" style="margin-bottom: 1.5rem;"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th style="width:110px;">Price (₹)</th>
                    <th style="width:90px;">Stock</th>
                    <th>Description</th>
                    <th>Cover URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <form method="POST" action="manage_books.php">
                    <input type="hidden" name="book_id" value="<?php echo $row['Book_ID']; ?>">
                    <tr class="edit-row">
                        <td>#<?php echo $row['Book_ID']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['Title']); ?></strong><br><small style="color:var(--text-muted);"><?php echo htmlspecialchars($row['Author_Name']); ?></small></td>
                        <td><?php echo htmlspecialchars($row['Author_Name']); ?></td>
                        <td><span class="badge badge-pending"><?php echo htmlspecialchars($row['Category_Name']); ?></span></td>
                        <td><input type="number" step="0.01" name="price" value="<?php echo $row['Price']; ?>" class="edit-input"></td>
                        <td><input type="number" name="stock" value="<?php echo $row['Stock']; ?>" class="edit-input"></td>
                        <td><textarea name="description" rows="2" class="edit-input" style="resize:vertical;"><?php echo htmlspecialchars($row['Description'] ?? ''); ?></textarea></td>
                        <td><input type="text" name="cover_url" value="<?php echo htmlspecialchars($row['Cover_Image_URL'] ?? ''); ?>" class="edit-input" placeholder="https://..."></td>
                        <td style="white-space:nowrap;">
                            <button type="submit" class="save-btn">💾 Save</button>
                            <a href="manage_books.php?delete=<?php echo $row['Book_ID']; ?>" class="del-btn" onclick="return confirm('Delete this book?')" style="display:block; margin-top:0.4rem;">🗑 Delete</a>
                        </td>
                    </tr>
                </form>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php $active = ($i == $page) ? 'background: var(--accent-color); color: white;' : 'background: white; color: var(--text-main);'; ?>
                <a href="manage_books.php?page=<?php echo $i; ?>" style="padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: 4px; text-decoration: none; font-weight: 500; <?php echo $active; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
