<?php
// Start session to access session variables
session_start();

// Check if a user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if the logged-in user has admin privileges
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Redirect users who are not logged in or are not admins
function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        // Send user to the login page if not authorized
        header("Location: ../users/login.php");
        exit;
    }
}
