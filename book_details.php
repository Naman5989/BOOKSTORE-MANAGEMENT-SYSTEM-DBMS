<?php
require_once 'db_connect.php';

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($book_id <= 0) die("Invalid book ID.");

// Fetch book details
$stmt = $conn->prepare("
    SELECT B.Book_ID, B.Title, B.ISBN, B.Price, B.Stock, B.Cover_Image_URL, B.Description, A.Author_Name, C.Category_Name
    FROM Books B
    JOIN Authors A ON B.Author_ID = A.Author_ID
    JOIN Categories C ON B.Category_ID = C.Category_ID
    WHERE B.Book_ID = ?
");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) die("Book not found.");

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);
    if ($rating >= 1 && $rating <= 5) {
        $r_stmt = $conn->prepare("INSERT INTO Reviews (Book_ID, User_ID, Rating, Review_Text) VALUES (?, ?, ?, ?)");
        $r_stmt->bind_param("iiis", $book_id, $_SESSION['user_id'], $rating, $review_text);
        $r_stmt->execute();
        header("Location: book_details.php?id=$book_id&reviewed=1");
        exit;
    }
}

// Fetch Reviews
$reviews = $conn->prepare("
    SELECT R.Rating, R.Review_Text, R.Created_At, U.Name 
    FROM Reviews R 
    JOIN Users U ON R.User_ID = U.User_ID 
    WHERE R.Book_ID = ? 
    ORDER BY R.Created_At DESC
");
$reviews->bind_param("i", $book_id);
$reviews->execute();
$reviews_result = $reviews->get_result();

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($book['Title']); ?> - Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📚 Bookstore</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="books.php">Browse</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
        <a href="cart.php">Cart <span class="cart-count"><?php echo $cart_count; ?></span></a>
    </nav>
</header>

<div class="container">
    <?php if (isset($_GET['reviewed'])): ?>
        <div class="success">Your review has been successfully submitted!</div>
    <?php endif; ?>

    <div class="book-details">
        <div>
            <img src="<?php echo htmlspecialchars($book['Cover_Image_URL']); ?>" alt="Cover" class="book-details-cover">
        </div>
        <div>
            <h2 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($book['Title']); ?></h2>
            <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 1.5rem;">by <?php echo htmlspecialchars($book['Author_Name']); ?> | <?php echo htmlspecialchars($book['Category_Name']); ?></p>
            
            <div style="font-size: 2rem; font-weight: 700; color: var(--accent-color); margin-bottom: 1.5rem;">
                ₹<?php echo number_format($book['Price'], 2); ?>
            </div>
            
            <p style="margin-bottom: 2rem; color: var(--text-main); line-height: 1.7; font-size: 1.05rem;">
                <?php echo nl2br(htmlspecialchars($book['Description'] ?? 'No description available for this book.')); ?>
            </p>
            
            <p style="margin-bottom: 1rem;"><strong>ISBN:</strong> <?php echo htmlspecialchars($book['ISBN']); ?></p>
            <p style="margin-bottom: 2rem;">
                <strong>Availability:</strong> 
                <?php if ($book['Stock'] > 0): ?>
                    <span style="color: #16A34A;">In Stock (<?php echo $book['Stock']; ?> available)</span>
                <?php else: ?>
                    <span style="color: #DC2626;">Out of Stock</span>
                <?php endif; ?>
            </p>

            <?php if ($book['Stock'] > 0): ?>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="book_id" value="<?php echo $book['Book_ID']; ?>">
                    <button type="submit" class="btn" style="padding: 1rem 3rem; font-size: 1.1rem;">Add to Cart</button>
                </form>
            <?php else: ?>
                <button class="btn" disabled style="background: #94A3B8; cursor: not-allowed;">Add to Cart</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="reviews-section">
        <h3 style="font-size: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">Customer Reviews</h3>
        
        <?php if ($reviews_result->num_rows > 0): ?>
            <div style="display: grid; gap: 1rem;">
                <?php while ($r = $reviews_result->fetch_assoc()): ?>
                    <div class="review-card">
                        <div class="stars"><?php echo str_repeat('★', $r['Rating']) . str_repeat('☆', 5 - $r['Rating']); ?></div>
                        <p style="font-weight: 600; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($r['Name']); ?> <span style="font-weight: 400; color: var(--text-muted); font-size: 0.85rem;">- <?php echo date('F j, Y', strtotime($r['Created_At'])); ?></span></p>
                        <p style="color: var(--text-main);"><?php echo nl2br(htmlspecialchars($r['Review_Text'])); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">No reviews yet. Be the first to review this book!</p>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="form-card" style="margin: 3rem 0 0 0; max-width: 100%;">
                <h4 style="margin-bottom: 1rem;">Write a Review</h4>
                <form method="POST" action="">
                    <input type="hidden" name="submit_review" value="1">
                    <div class="form-group">
                        <label>Rating</label>
                        <select name="rating" required>
                            <option value="5">5 Stars - Excellent</option>
                            <option value="4">4 Stars - Very Good</option>
                            <option value="3">3 Stars - Good</option>
                            <option value="2">2 Stars - Fair</option>
                            <option value="1">1 Star - Poor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Review (Optional)</label>
                        <textarea name="review_text" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Review</button>
                </form>
            </div>
        <?php else: ?>
            <p style="margin-top: 2rem;"><a href="login.php" style="color: var(--accent-color);">Login</a> to write a review.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
