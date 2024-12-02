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
    header("Location: book.php"); // Redirect to book list if no ID is provided
    exit();
}

$book_id = $_GET['id'];

// Fetch book details from the database
$book_query = $conn->query("SELECT * FROM books WHERE id = $book_id");
$book = $book_query->fetch_assoc();

if (!$book) {
    echo "Book not found.";
    exit();
}

// Fetch all categories
$categories_result = $conn->query("SELECT * FROM books_categories");
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category_id = $_POST['category_id'];
    $publication_year = $_POST['publication_year'];
    $isbn = $_POST['isbn'];
    $summary = $_POST['summary'];
    $status = $_POST['status']; // Get the status from the form
    $image = $book['image']; // Keep existing image if no new one is uploaded

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_image_name = "book_" . $book_id . "." . $image_extension;
        $upload_path = "../uploads/" . $new_image_name;

        // Move the uploaded image to the uploads directory
        if (move_uploaded_file($image_tmp, $upload_path)) {
            $image = $upload_path; // Update image with new image path
        }
    }

    // Update book details, including status
    $conn->query("UPDATE books
                  SET title = '$title', author = '$author', category_id = $category_id, 
                      publication_year = '$publication_year', isbn = '$isbn', summary = '$summary', 
                      status = '$status', image = '$image'
                  WHERE id = $book_id");

    header("Location: book.php"); // Redirect to book list after editing
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Include the Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1>Edit Book</h1>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" id="author" name="author" class="form-control" value="<?= htmlspecialchars($book['author']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= $category['id'] ?>" <?= $book['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="publication_year" class="form-label">Publication Year</label>
                    <input type="number" id="publication_year" name="publication_year" class="form-control" value="<?= $book['publication_year'] ?>">
                </div>
                <div class="mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" id="isbn" name="isbn" class="form-control" value="<?= htmlspecialchars($book['isbn']) ?>">
                </div>
                <div class="mb-3">
                    <label for="summary" class="form-label">Summary</label>
                    <textarea id="summary" name="summary" class="form-control" rows="5"><?= htmlspecialchars($book['summary']) ?></textarea>
                </div>

                <!-- Status Selection -->
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="available" <?= $book['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="not available" <?= $book['status'] == 'not available' ? 'selected' : '' ?>>Not Available</option>
                    </select>
                </div>


                <div class="mb-3">
                    <label for="image" class="form-label">Upload New Image (Optional)</label>
                    <input type="file" id="image" name="image" class="form-control">
                    <small class="form-text text-muted">Upload a new image if you want to replace the current one.</small>
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="book.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>