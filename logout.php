<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Optional: Clear session cookie (for extra security)
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Redirect to login page
header("Location: login.php");
exit();
