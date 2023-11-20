<?php
session_start();

// Unset and destroy the session
session_unset();
session_destroy();

// Redirect to the index.php page
header("Location: index.html");
exit;
?>
