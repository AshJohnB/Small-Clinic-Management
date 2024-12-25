<?php
session_start();
session_unset();  // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page (assuming login.php is also in public/)
header("Location: login.php");
exit();
