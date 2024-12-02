<?php
session_start();
include_once '../config/db.php'; // Include your database connection

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit();
}

// Get book ID from query string
if (!isset($_GET['id'])) {
    header("Location: ../login.php");
    exit();
}

$book_id = $_GET['id'];

// Fetch book details from the database
$book_query = $conn->query("
    SELECT b.id, b.title, b.author, c.name AS category, b.publication_year, b.isbn, b.summary, b.created_at, b.image, b.status 
    FROM books b
    JOIN books_categories c ON b.category_id = c.id
    WHERE b.id = $book_id
");

$book = $book_query->fetch_assoc();

if (!$book) {
    echo "Book not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Include the Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1>Book Details</h1>
            <div class="card">
                <div class="card-body">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($book['category']) ?></p>
                    <p><strong>Publication Year:</strong> <?= $book['publication_year'] ?? 'N/A' ?></p>
                    <p><strong>ISBN:</strong> <?= $book['isbn'] ?? 'N/A' ?></p>
                    <p><strong>Summary:</strong> <?= nl2br(htmlspecialchars($book['summary'])) ?></p>
                    <p><strong>Added On:</strong> <?= date('Y-m-d', strtotime($book['created_at'])) ?></p>

                    <!-- Display the status -->
                    <p><strong>Status:</strong> <?= htmlspecialchars($book['status']) ?></p>

                    <!-- Display the book image if it exists -->
                    <?php if (!empty($book['image'])): ?>
                        <div class="mt-3">
                            <strong>Book Image:</strong><br>
                            <img src="../uploads/<?= htmlspecialchars($book['image']) ?>" alt="Book Image" width="200">
                        </div>
                    <?php else: ?>
                        <p>No image available.</p>
                    <?php endif; ?>
                </div>
            </div>
            <a href="book.php" class="btn btn-primary mt-3">Back to Books</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
