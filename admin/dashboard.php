<?php
session_start();
include_once '../config/db.php'; // Include your database connection

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit();
}

// Get counts from the database
$user_count = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$category_count = $conn->query("SELECT COUNT(*) AS count FROM books_categories")->fetch_assoc()['count'];
$book_count = $conn->query("SELECT COUNT(*) AS count FROM books")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Add Bootstrap CSS for styling (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Include the Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid">
            <h1 class="mt-5">Admin Dashboard</h1>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Users Count</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $user_count ?></h5>
                            <p class="card-text">Total number of users.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Categories Count</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $category_count ?></h5>
                            <p class="card-text">Total number of book categories.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">Books Count</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $book_count ?></h5>
                            <p class="card-text">Total number of books in the library.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
