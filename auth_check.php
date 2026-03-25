<?php
session_start();

// If the session variable isn't set, kick them back to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Set a variable for the user's name to use in the HTML
$current_user = $_SESSION['full_name'] ?? 'Admin';
?>
