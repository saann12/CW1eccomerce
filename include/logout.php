<?php
// logout.php

// Include the session management file
require_once 'session.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Clear all session variables
    $_SESSION = [];

    // If you want to delete the session cookie, also clear it
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, 
                  $params["path"], $params["domain"], 
                  $params["secure"], $params["httponly"]);
    }

    // Destroy the session
    session_destroy();

    // Optionally, redirect to a logout confirmation page or login page
    header("Location: ../index.html"); // Redirect to homepage or login page
    exit();
} else {
    // Optionally, redirect to a page indicating the user is not logged in
    header("Location: ../index.html"); // Redirect to homepage or login page
    exit();
}
?>
