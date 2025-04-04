<?php
session_start();  // Start the session

// Destroy the session
session_destroy();  // Destroy all session data

// Redirect to the login page
header("Location: index.php");  // You can change this to wherever you want to redirect
exit();  // Make sure no further code is executed after the redirect
?>
