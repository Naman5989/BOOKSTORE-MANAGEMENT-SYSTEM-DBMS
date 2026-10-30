<?php 
require_once 'db_connect.php'; 
// Initialize cart session if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$cart_count = array_sum($_SESSION['cart']); // sum of quantities
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Online Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📚 Bookstore</h1>
    <nav>
        <a href="index.php" class="active">Home</a>
        <a href="books.php">Browse</a>
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

<div class="container">
    <div class="hero" style="background-image: url('images/hero.png');">
        <div class="hero-content">
            <h2>Discover Your Next Great Read</h2>
            <p>Explore thousands of books from top authors in our premium collection.</p>
            <form action="books.php" method="get" class="search-bar">
                <input type="text" name="q" placeholder="Search by title, author, or category..." required>
                <button type="submit" class="btn">Search</button>
            </form>
        </div>
    </div>

    <div class="section-title">
        <span>Featured Books</span>
        <a href="books.php" class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">View All</a>
    </div>

    <div class="books-grid">
        <?php
        $result = $conn->query("
            SELECT B.Book_ID, B.Title, A.Author_Name, C.Category_Name, B.Price, B.Cover_Image_URL, B.Description
            FROM Books B
            JOIN Authors A ON B.Author_ID = A.Author_ID
            JOIN Categories C ON B.Category_ID = C.Category_ID
            LIMIT 8
        ");
        while ($row = $result->fetch_assoc()) {
            echo '<div class="book-card">';
            echo '<a href="book_details.php?id=' . $row['Book_ID'] . '">';
            echo '<img src="' . htmlspecialchars($row['Cover_Image_URL']) . '" alt="Cover" class="book-cover">';
            echo '</a>';
            echo '<div class="book-info">';
            echo '<a href="book_details.php?id=' . $row['Book_ID'] . '" class="book-title">' . htmlspecialchars($row['Title']) . '</a>';
            echo '<div class="book-author">by ' . htmlspecialchars($row['Author_Name']) . '</div>';
            echo '<p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; line-height: 1.4; flex: 1;">' . htmlspecialchars(substr($row['Description'] ?? '', 0, 80)) . '...</p>';
            echo '<div class="book-price">₹' . number_format($row['Price'], 2) . '</div>';
            echo '<form action="add_to_cart.php" method="POST" style="margin-top:auto;">
                    <input type="hidden" name="book_id" value="'.$row['Book_ID'].'">
                    <button type="submit" class="btn" style="width:100%;">Add to Cart</button>
                  </form>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>

</body>
</html>
