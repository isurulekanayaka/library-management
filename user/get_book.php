<?php
// Start session to get the logged-in user details
session_start();

// Include your database connection
include_once '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// SQL query to get all borrowed books for the logged-in user
$query = "
    SELECT bb.id, bb.borrowed_at, bb.expire_at, bb.status, b.title, b.author, b.isbn, b.publication_year, b.image
    FROM borrowed_books bb
    JOIN books b ON bb.book_id = b.id
    WHERE bb.user_id = ? 
    ORDER BY bb.borrowed_at DESC
";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // Bind the logged-in user's ID
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Borrowed Books</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS (optional) -->
    <style>
        .book-image {
            width: 50px;
            height: 75px;
            object-fit: cover;
        }

        .cart-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
        }

        th, td {
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
        }

        .table-bordered {
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <!-- Navigation (Assuming you have a separate navigation component) -->
    <?php include '../components/navigation.php'; ?>

    <div class="container screen-h">
        <h3 class="mt-5">Your Borrowed Books</h3>

        <?php if ($result->num_rows > 0) : ?>
            <div class="cart-container">
                <!-- Table to display borrowed books -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Publication Year</th>
                            <th>Status</th>
                            <th>Borrowed At</th>
                            <th>Expire At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td>
                                    <img src="<?php echo (isset($row['image']) ? htmlspecialchars($row['image']) : 'default-image.jpg'); ?>" alt="Book Image" class="book-image" />
                                    <p><?php echo htmlspecialchars($row['title']); ?></p>
                                </td>
                                <td><?php echo htmlspecialchars($row['author']); ?></td>
                                <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                                <td><?php echo htmlspecialchars($row['publication_year']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                                <td><?php echo htmlspecialchars($row['borrowed_at']); ?></td>
                                <td><?php echo htmlspecialchars($row['expire_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p>You have not borrowed any books yet.</p>
        <?php endif; ?>
    </div>

    <!-- Footer (Assuming you have a separate footer component) -->
    <?php include '../components/footer.php'; ?>

    <!-- Bootstrap JS & Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
