<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Generator</title>
    <!-- link to tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 max-w-md w-full text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Welcome to Timetable Generator</h1>
        <p class="text-gray-600 mb-6">Please select your login type:</p>
        <div class="space-y-4">
            <a href="src/views/admin/login.php" class="block bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Login as Admin</a>
            <a href="src/views/teacher/login.php" class="block bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Login as Teacher</a>
            <a href="src/views/student/login.php" class="block bg-purple-500 text-white py-2 px-4 rounded hover:bg-purple-600">Login as Student</a>
        </div>
    </div>
</body>
</html>
