<?php
// Start session only if it’s not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if session not found
    header("Location: /Inventory_and_Billing_system/index.php");
    exit();
}
?>
