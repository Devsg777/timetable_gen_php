<?php
include_once "../../models/admin.php";
include_once __DIR__ . '/../../config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
session_destroy();
header("Location: login.php");
exit();




?>
