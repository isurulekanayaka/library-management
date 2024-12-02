<?php
session_start();
include_once '../config/db.php'; // Include database connection

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in or not an admin
    exit();
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $delete_query = "DELETE FROM books_categories WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    header("Location: categories.php"); // Redirect after delete
    exit();
}

// Fetch all categories from the database
$sql = "SELECT * FROM books_categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Categories</title>
    <!-- Add Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h1 class="mb-4">Manage Categories</h1>

        <!-- Button to Add Category -->
        <a href="add_category.php" class="btn btn-primary mb-3">Add New Category</a>

        <!-- Categories Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($category = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $category['id'] ?></td>
                            <td><?= $category['name'] ?></td>
                            <td>
                                <!-- Edit button -->
                                <a href="edit_category.php?id=<?= $category['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                
                                <!-- Delete button (with confirmation) -->
                                <a href="categories.php?delete=<?= $category['id'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No categories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
