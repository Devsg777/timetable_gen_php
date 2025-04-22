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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4gazinm2yYoNRYGjPT3hoPOGvPpWLmGKGSYrcXqvvcPWMEcTJQM+huCbYyKKzjFHtPXsdCSyQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen">
        <aside class="w-64 bg-blue-900 text-white p-6 flex flex-col">
            <div class="mb-8 flex items-center">
                <i class="fa-solid fa-chalkboard-user fa-2x mr-3"></i>
                <h2 class="text-2xl font-bold"><a href="dashboard.php" class="hover:text-blue-300 transition duration-300 ease-in-out">Teacher Hub</a></h2>
            </div>
            <nav class="flex-grow">
                <ul>
                <li class="mb-4">
                        <a href="teacher_profile.php" class="block p-3 rounded-md hover:bg-blue-800 transition duration-300 ease-in-out flex items-center">
                            <i class="fa-regular fa-user mr-3 fa-lg"></i>
                            Edit Profile
                        </a>
                    </li>
                    
                    <li class="mb-4">
                        <a href="sendRequest.php" class="block p-3 rounded-md hover:bg-blue-800 transition duration-300 ease-in-out flex items-center">
                            <i class="fa-solid fa-paper-plane mr-3 fa-lg"></i>
                            Request Change
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="teacherTimetable.php?id=<?= htmlspecialchars($_SESSION['teacher_id'] ?? ''); ?>" class="block p-3 rounded-md hover:bg-blue-800 transition duration-300 ease-in-out flex items-center">
                            <i class="fa-regular fa-calendar-check mr-3 fa-lg"></i>
                            My Timetable
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="../timetable_view.php" class="block p-3 rounded-md hover:bg-blue-800 transition duration-300 ease-in-out flex items-center">
                            <i class="fa-solid fa-table-columns mr-3 fa-lg"></i>
                            All Timetable
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-8">
                <a href="logout.php" class="block p-3 rounded-md bg-red-600 hover:bg-red-700 transition duration-300 ease-in-out flex items-center justify-center">
                    <i class="fa-solid fa-right-from-bracket mr-2 fa-lg"></i>
                    Logout
                </a>
            </div>
        </aside>

        <main class="flex-1 bg-gray-100 p-8">
        
        
                <!-- <p class="text-gray-700">This is your teacher dashboard. Here you can view your timetable, see the complete timetable, request changes to your schedule, and manage your profile information using the navigation on the left.</p> -->
 