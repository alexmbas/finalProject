<?php
// Start the session to access and clear session data
session_start();

// Remove all session variables
session_unset();

// Destroy the session entirely
session_destroy();

// Redirect the user back to the login page
header("Location: login.php");
exit;
