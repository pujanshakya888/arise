<?php
session_start();

// Destroy session data
session_unset();
session_destroy();

// Expire the admin_remember cookie
setcookie("admin_remember", "", time() - 3600, "/");

// Redirect to login page
header("Location: login.php");
exit();
?>
