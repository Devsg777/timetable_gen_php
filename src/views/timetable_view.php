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


$time_slots =  ['09:00 - 10:00', '10:00 - 11:00', '11:00 - 12:00', '12:00 - 13:00', '01:00 - 02:00', '02:00 - 03:00', '03:00 - 04:00', '04:00 - 05:00', '05:00 - 06:00', ];
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
            <!-- Timetable for each combination section-->
            <?php foreach (json_decode($combination["sections"]) as $section) : ?>
            <div class="max-w-7xl mx-auto bg-white p-2 rounded-lg shadow-md mb-3">
                <h2 class="text-xl font-bold mb-4 text-center">
                    <?= htmlspecialchars($combination['name']) . " ( Semester " . htmlspecialchars($combination['semester']) . " - " . htmlspecialchars($combination['department']) . " )".htmlspecialchars($section)." Section"; ?>
                </h2>
                <pre>
                <?php $data = $timetable->getTimetableByCombination($combination['combination_id'],$section); ?>
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
                        <?php foreach ($days as $index => $day) : ?>
                            <tr>
                                <!-- Day Column -->
                                <td class="border border-gray-300 p-2 bg-gray-100 font-bold"><?= htmlspecialchars($day); ?></td>

                                <?php
                                $slotCount = count($time_slots);
                                $slotIndex = 0;

                                // Loop through all time slots for the day
                                while ($slotIndex < $slotCount) :
                                    $slot = $time_slots[$slotIndex];

                                    // If it's the lunch break time slot (01:00 - 02:00), show lunch break text in the cell
                                    if ($slot === '01:00 - 02:00') {
                                ?>
                                        <td class="border border-gray-300 p-2 text-center bg-yellow-100 font-semibold text-yellow-700">
                                            🍽️ Lunch Break
                                        </td>
                                    <?php
                                        $slotIndex++; // Move to next slot after the lunch break
                                        continue;
                                    }

                                    // Check if there's data for this time slot
                                    if (isset($data[$day][$slot])) {
                                        $entry = $data[$day][$slot];

                                        // Count consecutive slots that belong to the same entry (for labs)
                                        $colspan = 1;
                                        for ($j = $slotIndex + 1; $j < $slotCount; $j++) {
                                            $nextSlot = $time_slots[$j];

                                            // If the next time slot belongs to the same entry, increment colspan
                                            if (isset($data[$day][$nextSlot]) && $data[$day][$nextSlot]['entry_id'] === $entry['entry_id']) {
                                                $colspan++;
                                            } else {
                                                break;
                                            }
                                        }

                                        // Detect lab sessions (either by colspan or by subject)
                                        $isLab = $colspan >= 2 || stripos($entry['subject'], 'lab') !== false;

                                        // Output table cell for this slot
                                    ?>
                                        <td class="border border-gray-300 p-2 text-sm text-center <?= $isLab ? 'bg-blue-100' : '' ?>" colspan="<?= $colspan ?>">
                                            <b><?= htmlspecialchars($entry['subject']); ?></b><br>
                                            <span class="text-gray-600">Teacher: <?= htmlspecialchars($entry['teacher']); ?></span><br>
                                            <span class="text-gray-500">Room: <?= htmlspecialchars($entry['classroom']); ?></span><br>
                                        </td>
                                    <?php
                                        // Skip over the slots merged as part of the same lab session
                                        $slotIndex += $colspan;
                                    } else {
                                        // No entry, show empty cell with add and swap buttons
                                    ?>
                                        <td class="border border-gray-300 p-2 text-center text-gray-400">
                                            -
                                        </td>
                                <?php
                                        $slotIndex++;
                                    }
                                endwhile;
                                ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>


                </table>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>


</body>
</html>
