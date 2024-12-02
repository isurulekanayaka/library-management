<?php
// Start the session
session_start();

// Destroy all session data
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
