<?php
include_once "../config/database.php";
include_once "./../models/Timetable.php";
include_once "./../models/Subject.php";
include_once "./../models/Teacher.php";
include_once "./../models/Classroom.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['teacher_id']) && !isset($_SESSION['student_id']) ) {
    header("Location: ../../index.php");
    exit();
}
$database = new Database();
$db = $database->getConnection();
$timetable = new Timetable($db);


// Fetch timetables for all combinations
$combinations = $timetable->getAllCombinations(); // Fetch distinct combinations


$time_slots =  ['09:00 - 10:00', '10:00 - 11:00', '11:00 - 12:00', '12:00 - 01:00', '01:00 - 02:00', '02:00 - 03:00', '03:00 - 04:00', '04:00 - 05:00'];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <button onclick="window.history.back()" class=" absolute t-0 right-0 mr-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mb-4">Go Back</button>
        <h1 class="text-3xl font-bold mb-6 text-center">Timetable Management</h1>

        <div class="flex space-x-4 mb-4">
            <a href="../../controllers/exportTimetable.php?type=excel" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Export to Excel</a>
            <a href="../../controllers/exportTimetable.php?type=pdf" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Export to PDF</a>
        </div>

        <?php foreach ($combinations as $combination) : ?>
            <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-bold mb-4 text-center">
                    <?= htmlspecialchars($combination['name']) . " (" . htmlspecialchars($combination['department']) . " - Semester " . htmlspecialchars($combination['semester']) . ")"; ?>
                </h2><pre>
                <?php $data = $timetable->getTimetableByCombination($combination['combination_id']); ?>
                </pre>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2">Day</th>
                            <?php foreach ($time_slots as $slot) : ?>
                                <th class="border border-gray-300 p-2"><?= htmlspecialchars($slot); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($days as $day) : ?>
                            <tr>
                                <td class="border border-gray-300 p-2 bg-gray-100 font-bold"><?= htmlspecialchars($day); ?></td>
                                <?php foreach ($time_slots as $slot) : ?>
                                    
                                    <?php if (isset($data[$day][$slot])) :
                                        $entry = $data[$day][$slot]; ?>
                                        <td class="border border-gray-300 p-2 text-sm text-center">
                                            <b><?= htmlspecialchars($entry['subject']); ?></b><br>
                                            <span class="text-gray-600">Teacher: <?= htmlspecialchars($entry['teacher']); ?></span><br>
                                            <span class="text-gray-500">Room: <?= htmlspecialchars($entry['classroom']); ?></span><br>
                                        </td>
                                    <?php else : ?>
                                        <td class="border border-gray-300 p-2 text-center text-gray-400">
                                            ---
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

</body>
</html>
