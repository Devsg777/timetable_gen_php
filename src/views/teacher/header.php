<?php
include_once "../../config/database.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-900 text-white p-5">
            <h2 class="text-2xl font-bold mb-5"><a href="dashboard.php" class="block p-2 rounded hover:bg-blue-700">Teacher Dashboard</a></h2>
            <ul>
                <li class="mb-3"><a href="teacherTimetable.php?id=<?= htmlspecialchars($_SESSION['teacher_id'] ?? ''); ?>" class="block p-2 rounded hover:bg-blue-700">View Timetable</a></li>
                <li class="mb-3"><a href="a" class="block p-2 rounded hover:bg-blue-700">Request Class Change</a></li>
                <li><a href="teacher_profile.php" class="block p-2 rounded hover:bg-blue-700">Edit Profile</a></li>
            </ul>
        </div>