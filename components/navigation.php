<nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
    <div class="container">
        <a class="navbar-brand" href="#">Library Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">All Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="get_book.php">Get Book</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user_profile.php">User Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='../logout.php'>Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Optional custom styling for navbar -->
<style>
    .navbar-nav .nav-link {
        padding: 8px 15px;
        color: white !important;
    }

    .navbar-nav .nav-link:hover {
        background-color: #555;
    }

    .screen-h {
        min-height: 100vh;
    }
</style>