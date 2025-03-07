<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 h-screen bg-blue-700 text-white p-5 fixed">
        <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
        <ul>
            <li class="mb-4">
                <a href="dashboard.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="combinations.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <i class="fas fa-layer-group"></i>
                    <span>Combinations</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="subject.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <i class="fas fa-book"></i>
                    <span>Subjects</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="teachers.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Teachers</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="students.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="classrooms.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <i class="fas fa-school"></i>
                    <span>Classrooms</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="timetable.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Timetable</span>
                </a>
            </li>
            <li class="mt-6">
                <a href="logout.php" class="flex items-center space-x-2 text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>
    <div class="flex-1 ml-64 p-6">