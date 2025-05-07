<?php
include_once "../../config/database.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex font-sans">

  <!-- Sidebar -->
  <aside class="w-64 h-screen bg-blue-800 text-white fixed flex flex-col p-6 shadow-lg">
    <h2 class="text-3xl font-bold mb-10 tracking-wide text-white">Admin Panel</h2>

    <nav class="flex-1">
      <ul class="space-y-4 text-base">
        <li>
          <a href="dashboard.php" class="flex items-center gap-3 hover:bg-blue-700 p-2 rounded transition">
            <i class="fas fa-home text-lg"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li>
          <a href="combinations.php" class="flex items-center gap-3 hover:bg-blue-700 p-2 rounded transition">
            <i class="fas fa-layer-group text-lg"></i>
            <span>Combinations</span>
          </a>
        </li>
        <li>
          <a href="subject.php" class="flex items-center gap-3 hover:bg-blue-700 p-2 rounded transition">
            <i class="fas fa-book text-lg"></i>
            <span>Subjects</span>
          </a>
        </li>
        <li>
          <a href="teachers.php" class="flex items-center gap-3 hover:bg-blue-700 p-2 rounded transition">
            <i class="fas fa-chalkboard-teacher text-lg"></i>
            <span>Teachers</span>
          </a>
        </li>
        <li>
          <a href="students.php" class="flex items-center gap-3 hover:bg-blue-700 p-2 rounded transition">
            <i class="fas fa-user-graduate text-lg"></i>
            <span>Students</span>
          </a>
        </li>
        <li>
          <a href="classrooms.php" class="flex items-center gap-3 hover:bg-blue-700 p-2 rounded transition">
            <i class="fas fa-school text-lg"></i>
            <span>Classrooms</span>
          </a>
        </li>
        <li>
          <a href="request.php" class="flex items-center gap-3 hover:bg-blue-700 p-2 rounded transition">
            <i class="fas fa-paper-plane text-lg"></i>
            <span>Requests</span>
          </a>
        </li>
        <li>
          <a href="timetable.php" class="flex items-center gap-3 hover:bg-blue-700 p-2 rounded transition">
            <i class="fas fa-calendar-alt text-lg"></i>
            <span>Timetable</span>
          </a>
        </li>
      </ul>
    </nav>

    <div class="mt-auto pt-6 border-t border-blue-700">
      <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:text-red-300 p-2 rounded transition">
        <i class="fas fa-sign-out-alt text-lg"></i>
        <span>Logout</span>
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 ml-64 p-6">