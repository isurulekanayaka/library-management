<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <h1>Welcome to the Library Management System</h1>
    </header>

    <div class="content">
        <?php
        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {
            // If logged in, show Dashboard or user-specific content
            echo "<p>Welcome, " . $_SESSION['role'] . "!</p>";

            if ($_SESSION['role'] == 'admin') {
                echo "<a href='admin/dashboard.php'>Go to Admin Dashboard</a>";
            } else {
                echo "<a href='user/index.php'>Go to User Dashboard</a>";
            }
            echo "<br><a href='logout.php'>Logout</a>";
        } else {
            // If not logged in, show the login link
            echo "<p>You are not logged in. Please <a href='login.php'>Login</a></p>";
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2024 Library Management System. All rights reserved.</p>
    </footer>
</body>

</html>