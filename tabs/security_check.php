<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in → redirect to login page
    header("Location: /Inventory_and_Billing_system/index.php");
    exit();
}
?>
