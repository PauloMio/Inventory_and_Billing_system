<?php
session_start();
session_unset();
session_destroy();
header("Location: /Inventory_and_Billing_system/index.php"); // if published in domain use this: "Location: /../index.php"
exit();
?>
