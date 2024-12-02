<?php
session_start();
include_once '../config/db.php'; // Include your database connection

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit();
}

// Fetch book categories for the dropdown
$categories_result = $conn->query("SELECT id, name FROM books_categories");
$categories = $categories_result ? $categories_result->fetch_all(MYSQLI_ASSOC) : [];

// Handle form submission
$success_message = $error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = intval($_POST['category_id']);
    $publication_year = intval($_POST['publication_year']);
    $isbn = trim($_POST['isbn']);
    $summary = trim($_POST['summary']);
    $status = trim($_POST['status']); // Capture the status

    // Validate input
    if (empty($title) || empty($author) || empty($category_id) || empty($isbn) || empty($status)) {
        $error_message = "All required fields must be filled.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error_message = "Please select a valid image.";
    } else {
        // Handle image upload
        $image = $_FILES['image'];
        $uploads_dir = '../uploads';
        
        // Ensure the uploads directory exists
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        // Generate a unique filename
        $image_ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('img_', true) . '.' . $image_ext;
        $image_path = $uploads_dir . '/' . $image_name;

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($image['tmp_name'], $image_path)) {
            // Insert the new book into the database
            $stmt = $conn->prepare("INSERT INTO books (title, author, category_id, publication_year, isbn, summary, image, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssisssss", $title, $author, $category_id, $publication_year, $isbn, $summary, $image_name, $status);

            if ($stmt->execute()) {
                $success_message = "Book added successfully!";
            } else {
                $error_message = "Failed to add the book. Please try again.";
            }

            $stmt->close();
        } else {
            $error_message = "Failed to upload the image.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Include the Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Add New Book</h1>

            <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="image" class="form-label">Book Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Book Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" class="form-control" id="author" name="author" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="publication_year" class="form-label">Publication Year</label>
                    <input type="number" class="form-control" id="publication_year" name="publication_year">
                </div>

                <div class="mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" class="form-control" id="isbn" name="isbn">
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label">Summary</label>
                    <textarea class="form-control" id="summary" name="summary" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="available">Available</option>
                        <option value="not available">Not Available</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Add Book</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
