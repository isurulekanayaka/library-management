<?php
session_start();
include_once '../config/db.php'; // Include database connection

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in or not an admin
    exit();
}

// Get category ID from URL
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Fetch category details from the database
    $sql = "SELECT * FROM books_categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if category is found
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        echo "Category not found.";
        exit();
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $category_name = $_POST['category_name'];

        // Update category in the database
        $update_sql = "UPDATE books_categories SET name = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('si', $category_name, $category_id);
        if ($update_stmt->execute()) {
            header("Location: categories.php"); // Redirect after successful update
            exit();
        } else {
            $error = "Error updating category!";
        }
    }
} else {
    header("Location: categories.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <!-- Add Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Edit Category</h1>

        <!-- Display error if any -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Edit Category Form -->
        <form action="edit_category.php?id=<?= $category['id'] ?>" method="POST">
            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" value="<?= $category['name'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>

        <a href="categories.php" class="btn btn-secondary mt-3">Back to Categories</a>
    </div>

    <!-- Add Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
