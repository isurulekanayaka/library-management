<?php
session_start();
include_once '../config/db.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch all borrowed books for all users
$query = "SELECT bb.id, b.title, b.author, bb.borrowed_at, bb.expire_at, bb.status, u.name AS user_name
          FROM borrowed_books bb
          JOIN books b ON bb.book_id = b.id
          JOIN users u ON bb.user_id = u.id";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Handle the delete action (delete borrowed book)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM borrowed_books WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();

    header("Location: borrow.php"); // Reload the page after deleting a borrowed book
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Include the Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">

            <!-- Borrow Book Button -->
            <a href="add_borrow_book.php" class="btn btn-primary mb-3">Borrow Book</a>

            <h4>All Borrowed Books</h4>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Borrowed At</th>
                        <th>Expire At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($borrowed_book = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($borrowed_book['user_name']) ?></td>
                            <td><?= htmlspecialchars($borrowed_book['title']) ?></td>
                            <td><?= htmlspecialchars($borrowed_book['author']) ?></td>
                            <td><?= date('Y-m-d', strtotime($borrowed_book['borrowed_at'])) ?></td>
                            <td><?= date('Y-m-d', strtotime($borrowed_book['expire_at'])) ?></td>
                            <td><?= ucfirst($borrowed_book['status']) ?></td>
                            <td>
                                <!-- View Button -->
                                <a href="view_borrow.php?id=<?= $borrowed_book['id'] ?>" class="btn btn-info btn-sm">View</a>
                                
                                <!-- Edit Button -->
                                <a href="edit_borrow.php?id=<?= $borrowed_book['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                
                                <!-- Delete Button -->
                                <a href="?delete_id=<?= $borrowed_book['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
