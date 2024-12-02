<?php
session_start();
include_once '../config/db.php'; // Include your database connection

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle book deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Prepare the delete query
    $delete_query = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $delete_id);

    if ($stmt->execute()) {
        // Redirect after successful deletion
        header("Location: book.php?message=Book deleted successfully.");
        exit();
    } else {
        // If deletion failed
        header("Location: book.php?error=Failed to delete book.");
        exit();
    }
}
// Fetch all categories for the filter dropdown
$categories_result = $conn->query("SELECT * FROM books_categories");
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// Fetch books based on filter criteria (for AJAX)
$whereClauses = [];
$params = [];

if (isset($_POST['title']) && $_POST['title'] != '') {
    $whereClauses[] = "b.title LIKE ?";
    $params[] = "%" . $_POST['title'] . "%";
}
if (isset($_POST['author']) && $_POST['author'] != '') {
    $whereClauses[] = "b.author LIKE ?";
    $params[] = "%" . $_POST['author'] . "%";
}
if (isset($_POST['category']) && $_POST['category'] != '') {
    $whereClauses[] = "b.category_id = ?";
    $params[] = $_POST['category'];
}
if (isset($_POST['status']) && $_POST['status'] != '') {
    $whereClauses[] = "b.status = ?";
    $params[] = $_POST['status'];
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(' AND ', $whereClauses);
}

$query = "
    SELECT b.id, b.title, b.author, c.name AS category, b.publication_year, b.isbn, b.image, b.created_at, b.status 
    FROM books b
    JOIN books_categories c ON b.category_id = c.id
    $whereSql
";
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$books_result = $stmt->get_result();
$books = $books_result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Include the Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Books Management</h1>
                <a href="add_book.php" class="btn btn-primary">Add Book</a>
            </div>

            <!-- Filter Form -->
            <div class="mb-4">
                <form id="filterForm" method="post" action="book.php">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="title" name="title" placeholder="Title">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="author" name="author" placeholder="Author">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="category" name="category">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="status" name="status">
                                <option value="">Select Status</option>
                                <option value="available">Available</option>
                                <option value="not available">Not Available</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table to display books -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="booksTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Publication Year</th>
                            <th>ISBN</th>
                            <th>Status</th> <!-- New Status Column -->
                            <th>Added On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($books) > 0) : ?>
                            <?php foreach ($books as $index => $book) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <?php if (!empty($book['image'])) : ?>
                                            <img src="../uploads/<?= htmlspecialchars($book['image']) ?>" alt="Book Image" style="width: 50px; height: auto;">
                                        <?php else : ?>
                                            <span>No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td><?= htmlspecialchars($book['category']) ?></td>
                                    <td><?= $book['publication_year'] ?? '-' ?></td>
                                    <td><?= $book['isbn'] ?? '-' ?></td>
                                    <td><?= htmlspecialchars($book['status']) ?></td>
                                    <td><?= date('Y-m-d', strtotime($book['created_at'])) ?></td>
                                    <td>
                                        <a href="view_book.php?id=<?= $book['id'] ?>" class="btn btn-info btn-sm">View</a>
                                        <a href="edit_book.php?id=<?= $book['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="book.php?delete_id=<?= $book['id'] ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="9" class="text-center">No books found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // When any filter changes, submit the form via AJAX
            $('#filterForm select, #filterForm input').on('change', function() {
                var formData = $('#filterForm').serialize(); // Serialize form data
                $.ajax({
                    url: 'book.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Replace table body with filtered results
                        $('#booksTable tbody').html($(response).find('#booksTable tbody').html());
                    }
                });
            });
        });
    </script>
</body>

</html>