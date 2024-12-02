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
               u.name AS user_name, b.title AS book_title
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

// Handle form submission for editing the borrowed book
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $expire_at = $_POST['expire_at'];
    $status = $_POST['status'];

    // Update the borrowed book details
    $update_query = "
        UPDATE borrowed_books
        SET expire_at = ?, status = ?
        WHERE id = ?
    ";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ssi', $expire_at, $status, $borrowed_book_id);
    $stmt->execute();

    // Also update the book's availability status based on the borrowed status
    $book_status = ($status == 'returned') ? 'available' : 'not available';
    $update_book_query = "
        UPDATE books
        SET status = ?
        WHERE id = (SELECT book_id FROM borrowed_books WHERE id = ?)
    ";
    $stmt = $conn->prepare($update_book_query);
    $stmt->bind_param('si', $book_status, $borrowed_book_id);
    $stmt->execute();

    // Redirect back to the borrowed books page
    header("Location: borrow.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Borrowed Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container p-4">
        <a href="borrow.php" class="btn btn-secondary mb-3">Back to Borrowed Books</a>
        <h4>Edit Borrowed Book</h4>

        <form method="POST">
            <div class="mb-3">
                <label for="book_title" class="form-label">Book Title</label>
                <input type="text" class="form-control" id="book_title" value="<?= htmlspecialchars($borrowed_book['book_title']) ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="user_name" class="form-label">User Name</label>
                <input type="text" class="form-control" id="user_name" value="<?= htmlspecialchars($borrowed_book['user_name']) ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="expire_at" class="form-label">Expire At</label>
                <input type="date" class="form-control" id="expire_at" name="expire_at" value="<?= htmlspecialchars($borrowed_book['expire_at']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="borrowed" <?= $borrowed_book['status'] == 'borrowed' ? 'selected' : '' ?>>Borrowed</option>
                    <option value="returned" <?= $borrowed_book['status'] == 'returned' ? 'selected' : '' ?>>Returned</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
