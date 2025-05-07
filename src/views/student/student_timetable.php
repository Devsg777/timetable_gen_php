<?php
include_once "./../../config/database.php";
include_once "./../../models/Timetable.php";
include_once "./../../models/Combination.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['teacher_id']) && !isset($_SESSION['student_id'])) {
    header("Location: ../../index.php");
    exit();
}
$stu_comb= $_SESSION['student_combination'];
$stu_sec =$_SESSION['student_section']; 
$database = new Database();
$db = $database->getConnection();
$timetable = new Timetable($db);

// Fetch timetables for all combinations
$combinations = $timetable->getAllCombinations(); // Fetch distinct combinations

$time_slots = ['10:00 - 11:00', '11:00 - 12:00', '12:00 - 13:00', '01:00 - 02:00', '02:00 - 03:00', '03:00 - 04:00', '04:00 - 05:00', '05:00 - 06:00'];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

$flag = false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4gazinm2yYoNRYGjPT3hoPOGvPpWLmGKGSYrcXqvvcPWMEcTJQM+huCbYyKKzjFHtPXsdCSyQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100 p-6 font-sans antialiased">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <button onclick="window.history.back()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition duration-300 ease-in-out">
                <i class="fas fa-arrow-left mr-2"></i> Go Back
            </button>
            <h1 class="text-3xl font-bold text-center text-blue-700">Timetable Management</h1>
            <div>
                <a href="../../controllers/exportTimetable.php?type=excel" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition duration-300 ease-in-out">
                    <i class="fas fa-file-excel mr-2"></i> Export to Excel
                </a>
                <a href="../../controllers/exportTimetable.php?type=pdf" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-300 ease-in-out ml-2">
                    <i class="fas fa-file-pdf mr-2"></i> Export to PDF
                </a>
            </div>
        </div>

        <?php foreach ($combinations as $combination) :  ?>
            <?php if ($combination['combination_id'] == ($stu_comb )) : $flag=true; ?>
                
                <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <h2 class="text-xl font-bold mb-4 text-center text-indigo-600">
                        <?= htmlspecialchars($combination['name']) . " (" . htmlspecialchars($combination['department']) . " - Semester " . htmlspecialchars($combination['semester']) . $_SESSION['student_section'].")"; ?>
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border border-gray-300 p-3 text-left font-semibold text-gray-700">Day/Time</th>
                                    <?php foreach ($time_slots as $slot) : ?>
                                        <th class="border border-gray-300 p-3 text-center font-semibold text-gray-700"><?= htmlspecialchars($slot); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $data = $timetable->getTimetableByCombination($combination['combination_id'],$stu_sec); ?>
                                <?php foreach ($days as $day) : ?>
                                    <tr>
                                        <td class="border border-gray-300 p-3 bg-gray-100 font-bold text-gray-800"><?= htmlspecialchars($day); ?></td>
                                        <?php foreach ($time_slots as $slot) : ?>
                                            <?php if (isset($data[$day][$slot])) :
                                                $entry = $data[$day][$slot]; ?>
                                                <td class="border border-gray-300 p-3 text-sm text-center">
                                                    <div class="font-semibold text-blue-600"><?= htmlspecialchars($entry['subject']); ?></div>
                                                    <div class="text-gray-600"><i class="fas fa-user-tie mr-1"></i><?= htmlspecialchars($entry['teacher']); ?></div>
                                                    <div class="text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($entry['classroom']); ?></div>
                                                </td>
                                            <?php else : ?>
                                                <td class="border border-gray-300 p-3 text-center text-gray-400">
                                                    <i class="fas fa-minus-circle"></i> Empty
                                                </td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>    
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if(!$flag) :?>
            <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <p class="text-center text-gray-600 italic">Your Combination Timetable has not been designed yet.</p>
                </div>
    <?php endif; ?>         

    </div>
</body>
</html>