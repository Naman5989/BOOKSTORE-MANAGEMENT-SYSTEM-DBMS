<?php
require_once 'db_connect.php';

// Filter inputs
$q = trim($_GET['q'] ?? '');
$cat_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : 99999;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$where_clauses = ["1=1"];
$params = [];
$types = "";

if ($q !== '') {
    $where_clauses[] = "(B.Title LIKE ? OR A.Author_Name LIKE ?)";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}
if ($cat_id > 0) {
    $where_clauses[] = "B.Category_ID = ?";
    $params[] = $cat_id;
    $types .= "i";
}
if ($min_price > 0) {
    $where_clauses[] = "B.Price >= ?";
    $params[] = $min_price;
    $types .= "d";
}
if ($max_price < 99999) {
    $where_clauses[] = "B.Price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

$where_sql = implode(" AND ", $where_clauses);

// Count total
$count_sql = "SELECT COUNT(*) as total FROM Books B JOIN Authors A ON B.Author_ID = A.Author_ID WHERE $where_sql";
$count_stmt = $conn->prepare($count_sql);
if ($types) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_rows = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch books
$data_sql = "
    SELECT B.Book_ID, B.Title, A.Author_Name, C.Category_Name, B.Price, B.Cover_Image_URL, B.Description 
    FROM Books B
    JOIN Authors A ON B.Author_ID = A.Author_ID
    JOIN Categories C ON B.Category_ID = C.Category_ID
    WHERE $where_sql
    LIMIT ? OFFSET ?
";
$data_stmt = $conn->prepare($data_sql);
$types .= "ii";
$params[] = $limit;
$params[] = $offset;
$data_stmt->bind_param($types, ...$params);
$data_stmt->execute();
$result = $data_stmt->get_result();

// Fetch categories for sidebar
$categories = $conn->query("SELECT * FROM Categories ORDER BY Category_Name");

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Browse Books - Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📚 Bookstore</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="books.php" class="active">Browse</a>
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
        <a href="cart.php">Cart <span class="cart-count"><?php echo $cart_count; ?></span></a>
    </nav>
</header>

<div class="container layout-sidebar">
    <aside class="sidebar">
        <h3>Filters</h3>
        <form method="GET" action="books.php">
            <div class="form-group">
                <label>Search</label>
                <input type="text" name="q" placeholder="Keywords..." value="<?php echo htmlspecialchars($q); ?>">
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <option value="0">All Categories</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['Category_ID']; ?>" <?php if ($cat_id == $cat['Category_ID']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['Category_Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Min Price (₹)</label>
                <input type="number" name="min_price" value="<?php echo $min_price > 0 ? $min_price : ''; ?>" placeholder="Min">
            </div>
            
            <div class="form-group">
                <label>Max Price (₹)</label>
                <input type="number" name="max_price" value="<?php echo $max_price < 99999 ? $max_price : ''; ?>" placeholder="Max">
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Apply Filters</button>
            <a href="books.php" class="btn btn-outline" style="width: 100%; margin-top: 0.5rem; text-align: center; display: block;">Reset</a>
        </form>
    </aside>

    <main>
        <div class="section-title">
            <span>Books (<?php echo $total_rows; ?> found)</span>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="books-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="book-card">
                        <a href="book_details.php?id=<?php echo $row['Book_ID']; ?>">
                            <img src="<?php echo htmlspecialchars($row['Cover_Image_URL']); ?>" alt="Cover" class="book-cover">
                        </a>
                        <div class="book-info">
                            <a href="book_details.php?id=<?php echo $row['Book_ID']; ?>" class="book-title"><?php echo htmlspecialchars($row['Title']); ?></a>
                            <div class="book-author">by <?php echo htmlspecialchars($row['Author_Name']); ?></div>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; line-height: 1.4; flex: 1;">
                                <?php echo htmlspecialchars(substr($row['Description'] ?? '', 0, 80)) . '...'; ?>
                            </p>
                            <div class="book-price">₹<?php echo number_format($row['Price'], 2); ?></div>
                            <form action="add_to_cart.php" method="POST" style="margin-top:auto;">
                                <input type="hidden" name="book_id" value="<?php echo $row['Book_ID']; ?>">
                                <button type="submit" class="btn" style="width:100%;">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="margin-top: 3rem; display: flex; justify-content: center; gap: 0.5rem;">
                    <?php
                    $qs = $_GET;
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $qs['page'] = $i;
                        $link = 'books.php?' . http_build_query($qs);
                        $active = ($i == $page) ? 'background: var(--accent-color); color: white;' : 'background: white; color: var(--text-main);';
                        echo "<a href='$link' style='padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: 4px; text-decoration: none; font-weight: 500; $active'>$i</a>";
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 0;">
                <h3 style="color: var(--text-muted);">No books found matching your criteria.</h3>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
