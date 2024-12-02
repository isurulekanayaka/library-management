<?php
session_start();
include_once '../config/db.php'; // Include your database connection

// Check if user is logged in and has admin role (optional)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch all available books from the database
$query_books = "SELECT * FROM books WHERE id NOT IN (SELECT book_id FROM borrowed_books WHERE user_id = {$_SESSION['user_id']})";
$result_books = $conn->query($query_books);

// Fetch all users (to allow an admin to select a user)
$query_users = "SELECT id, name FROM users";
$result_users = $conn->query($query_users);

// Handle the borrow action (add borrowed book)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow'])) {
    $user_id = $_POST['user_id']; // The user who is borrowing the book
    $book_id = $_POST['book_id'];
    $expire_at = $_POST['expire_at'];

    // Insert the borrowed book record
    $stmt = $conn->prepare("INSERT INTO borrowed_books (user_id, book_id, expire_at, status) VALUES (?, ?, ?, 'borrowed')");
    $stmt->bind_param('iis', $user_id, $book_id, $expire_at);

    if ($stmt->execute()) {
        // Update the status of the book to 'not available' since it has been borrowed
        $update_book_status = $conn->prepare("UPDATE books SET status = 'not available' WHERE id = ?");
        $update_book_status->bind_param('i', $book_id);
        $update_book_status->execute();

        header("Location: borrow.php"); // Redirect to the borrow page after borrowing a book
        exit();
    } else {
        $error_message = "Error borrowing the book. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow a Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Include the Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h4>Borrow a Book</h4>

            <?php if (isset($error_message)) : ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <form method="POST">
                <!-- Select User (only for admins or authorized users) -->
                <div class="mb-3">
                    <label for="user_id" class="form-label">Select User</label>
                    <select id="user_id" name="user_id" class="form-control" required>
                        <option value="">Choose a user</option>
                        <?php while ($user = $result_users->fetch_assoc()) : ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> - <?= htmlspecialchars($user['id']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Select Book -->
                <div class="mb-3">
                    <label for="book_id" class="form-label">Select a Book</label>
                    <select id="book_id" name="book_id" class="form-control" required>
                        <option value="">Choose a book</option>
                        <?php while ($book = $result_books->fetch_assoc()) : ?>
                            <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?> by <?= htmlspecialchars($book['author']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Expiration Date -->
                <div class="mb-3">
                    <label for="expire_at" class="form-label">Expiration Date</label>
                    <input type="date" id="expire_at" name="expire_at" class="form-control" required>
                </div>

                <button type="submit" name="borrow" class="btn btn-primary">Borrow Book</button>
            </form>
            <br>
            <a href="borrow.php" class="btn btn-secondary">Back to Borrowed Books</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
