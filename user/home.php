<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once '../config/db.php'; // Include your database connection

// Check if user is logged in and has user privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php"); 
    exit();
}

// Get filter parameters from the form submission
$title = isset($_POST['title']) ? $_POST['title'] : '';
$author = isset($_POST['author']) ? $_POST['author'] : '';
$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'available';

// Fetch filtered books from the database only if form is submitted
$query = "SELECT * FROM books WHERE title LIKE ? AND author LIKE ? AND category_id LIKE ?";

// Add condition for status if it's not "all"
if ($status !== 'all') {
    $query .= " AND status = ?";
}

$stmt = $conn->prepare($query);
$search_title = "%$title%";
$search_author = "%$author%";
$search_category_id = "%$category_id%";

// Bind parameters conditionally based on status
if ($status !== 'all') {
    $stmt->bind_param("ssss", $search_title, $search_author, $search_category_id, $status);
} else {
    $stmt->bind_param("sss", $search_title, $search_author, $search_category_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home - Library Management System</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Custom styling for fixed image size in the card */
        .card-img-top {
            width: 100%;  /* Make the image fill the width of the card */
            height: 200px;  /* Set a fixed height for all images */
            object-fit: cover;  /* Ensure the image maintains its aspect ratio and is cropped if needed */
        }

    </style>
</head>

<body>
<?php include '../components/navigation.php'; ?>
    <div class="container mt-4 screen-h">
        <h2>Welcome to the Library Management System</h2>

        <h3>Filter Available Books</h3>
        <form method="POST" action="home.php" id="filterForm">
            <div class="row mb-4">
                <div class="col-md-3">
                    <input type="text" name="title" class="form-control" placeholder="Title" value="<?php echo htmlspecialchars($title); ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="author" class="form-control" placeholder="Author" value="<?php echo htmlspecialchars($author); ?>">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <!-- Fetch categories from the database -->
                        <?php
                        $category_query = "SELECT * FROM books_categories";
                        $category_result = $conn->query($category_query);
                        while ($category = $category_result->fetch_assoc()) {
                            $selected = $category['id'] == $category_id ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($category['id']) . "' $selected>" . htmlspecialchars($category['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="all" <?php echo $status == 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="available" <?php echo $status == 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="not available" <?php echo $status == 'not available' ? 'selected' : ''; ?>>Not Available</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>

        <div id="bookResults" class="mt-4">
            <?php
            if ($result->num_rows > 0) {
                echo "<h3>Books</h3>";
                echo "<div class='row'>";  // Bootstrap grid starts

                // Display all books
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-4 mb-4'>";  // 3 columns per row in medium screens
                    echo "<div class='card' style='width: 18rem;'>";  // Bootstrap card
                    echo "<img src='" . (isset($row['image']) ? htmlspecialchars($row['image']) : 'default-image.jpg') . "' class='card-img-top' alt='Book Image'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($row['title']) . "</h5>";
                    echo "<p class='card-text'><strong>Author:</strong> " . htmlspecialchars($row['author']) . "</p>";
                    echo "<p class='card-text'><strong>ISBN:</strong> " . htmlspecialchars($row['isbn']) . "</p>";
                    echo "<p class='card-text'><strong>Publication Year:</strong> " . htmlspecialchars($row['publication_year']) . "</p>";
                    echo "<p class='card-text'><strong>Summary:</strong> " . nl2br(htmlspecialchars($row['summary'])) . "</p>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";  // End of col-md-4
                }

                echo "</div>";  // End of row
            } else {
                echo "<p>No books found with the applied filters.</p>";
            }
            ?>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>


    <!-- Add Bootstrap JS (optional for some components like modal) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
