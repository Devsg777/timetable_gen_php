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
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-900 text-white p-5">
            <h2 class="text-2xl font-bold mb-5"><a href="dashboard.php" class="block p-2 rounded hover:bg-blue-700">Student Dashboard</a></h2>
            <ul>
                <li class="mb-3"><a href="teacherTimetable.php?id=<?= htmlspecialchars($_SESSION['student_id'] ?? ''); ?>" class="block p-2 rounded hover:bg-blue-700">My Timetable</a></li>
                <li class="mb-3"><a href="../timetable_view.php" class="block p-2 rounded hover:bg-blue-700">All Timetable</a></li>
                <li class="mb-3"><a href="sendRequest.php" class="block p-2 rounded hover:bg-blue-700">Request Class Change</a></li>
                <li><a href="teacher_profile.php" class="block p-2 rounded hover:bg-blue-700">Edit Profile</a></li>
                <li><a href="logout.php" class="block p-2 rounded hover:bg-blue-700">Logout</a></li>
            </ul>
        </div>
        