<?php
session_start();
include_once '../config/db.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the borrowed book ID from the URL
if (isset($_GET['id'])) {
    $borrowed_book_id = $_GET['id'];

    // Fetch borrowed book details along with user and book details
    $query = "
        SELECT bb.id, bb.borrowed_at, bb.expire_at, bb.status,
               u.name AS user_name, u.email AS user_email,
               b.title AS book_title, b.author AS book_author, b.isbn AS book_isbn, b.summary AS book_summary
        FROM borrowed_books bb
        JOIN books b ON bb.book_id = b.id
        JOIN users u ON bb.user_id = u.id
        WHERE bb.id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $borrowed_book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the borrowed book exists
    if ($result->num_rows == 0) {
        echo "Borrowed book not found.";
        exit();
    }

    // Fetch the details of the borrowed book
    $borrowed_book = $result->fetch_assoc();
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Borrowed Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container p-4">
        <a href="borrow.php" class="btn btn-secondary mb-3">Back to Borrowed Books</a>
        <h4>Borrowed Book Details</h4>

        <div class="card">
            <div class="card-header">
                Book Information
            </div>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($borrowed_book['book_title']) ?></h5>
                <p><strong>Author:</strong> <?= htmlspecialchars($borrowed_book['book_author']) ?></p>
                <p><strong>ISBN:</strong> <?= htmlspecialchars($borrowed_book['book_isbn']) ?></p>
                <p><strong>Summary:</strong> <?= nl2br(htmlspecialchars($borrowed_book['book_summary'])) ?></p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Borrower Information
            </div>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($borrowed_book['user_name']) ?></h5>
                <p><strong>Email:</strong> <?= htmlspecialchars($borrowed_book['user_email']) ?></p>
                <p><strong>Borrowed At:</strong> <?= date('Y-m-d', strtotime($borrowed_book['borrowed_at'])) ?></p>
                <p><strong>Expire At:</strong> <?= date('Y-m-d', strtotime($borrowed_book['expire_at'])) ?></p>
                <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($borrowed_book['status'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
