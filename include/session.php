<?php
// session.php

// Start the session or resume the current one
session_start();

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 3600, // 1 hour
    'path' => '/',
    'domain' => '', // Set to your domain
    'secure' => isset($_SERVER['HTTPS']), // true if using HTTPS
    'httponly' => true, // Helps prevent JavaScript access to the cookie
    'samesite' => 'Strict', // or 'Lax', 'None' depending on your needs
]);

// Regenerate session ID to prevent fixation attacks
if (isset($_SESSION['CREATED']) && (time() - $_SESSION['CREATED'] > 1800)) {
    // Session started more than 30 minutes ago
    session_regenerate_id(true); // Regenerate session ID and delete old session
    $_SESSION['CREATED'] = time(); // Update creation time
} else {
    $_SESSION['CREATED'] = time(); // Set creation time
}

// Set a session timeout
$session_timeout = 3600; // 1 hour in seconds
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $session_timeout) {
    // Last request was more than $session_timeout seconds ago
    session_unset(); // Clear session variables
    session_destroy(); // Destroy the session
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Handle session flash messages or alerts
function set_flash_message($key, $message) {
    $_SESSION['flash_messages'][$key] = $message;
}

function get_flash_message($key) {
    if (isset($_SESSION['flash_messages'][$key])) {
        $message = $_SESSION['flash_messages'][$key];
        unset($_SESSION['flash_messages'][$key]);
        return $message;
    }
    return null;
}
?>
