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
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet"> <!-- Google Fonts -->
    <style>
        /* General body styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            /* Soft gray background */
            color: #333;
        }

        /* Header styling */
        header {
            background-color: #007bff;
            color: white;
            padding: 60px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            font-size: 3rem;
            font-weight: 600;
        }

        /* Footer styling */
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Main content */
        .container {
            margin-top: 30px;
        }

        .card {
            border-radius: 15px;
            border: none;
            background-color: #ffffff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 40px;
        }

        .btn {
            border-radius: 50px;
            /* Rounded buttons */
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-5px);
            /* Subtle hover effect */
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .h4 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }

        /* Card hover effect */
        .card:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .screen-h {
            min-height: 60vh;
            display: flex;
            /* Enables Flexbox */
            flex-direction: column;
            /* Aligns items from top to bottom */
            justify-content: center;
            /* Vertically centers the content */
            align-items: center;
            /* Horizontally centers the content */
        }

        /* Responsive design */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <header class="text-center">
        <h1>Welcome to the Library Management System</h1>
    </header>

    <!-- Main Content Section -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12 screen-h">
                <div class="card">
                    <div class="card-body">
                        <?php
                        // Check if user is logged in
                        if (isset($_SESSION['user_id'])) {
                            // If logged in, show Dashboard or user-specific content
                            echo "<p class='h4 text-center'>Welcome, " . $_SESSION['role'] . "!</p>";

                            if ($_SESSION['role'] == 'admin') {
                                echo "<a href='admin/dashboard.php' class='btn btn-primary w-100 mb-3'>Go to Admin Dashboard</a>";
                            } else {
                                echo "<a href='user/home.php' class='btn btn-secondary w-100 mb-3'>Go to User Dashboard</a>";
                            }
                            echo "<a href='logout.php' class='btn btn-danger w-100'>Logout</a>";
                        } else {
                            // If not logged in, show the login link
                            echo "<p class='text-center'>You are not logged in. Please <a href='login.php'>Login</a></p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="text-center">
        <p>&copy; 2024 Library Management System. All rights reserved.</p>
    </footer>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>