<?php include "./header.php";
include_once "../../config/database.php";
include_once "../../models/Timetable.php";

$database = new Database();
$db = $database->getConnection();
$timetable = new Timetable($db);
$timetable_data = $timetable->getTimetableByTeacherId_Data($_SESSION['teacher_id']);

?>

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <h3>Welcome</h3>
            <!-- Timetable Section -->
            <section id="timetable" class="mb-10">
                <h2 class="text-xl font-semibold mb-4">Your Timetable</h2>
                <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="p-3">Day</th>
                            <th class="p-3">Subject</th>
                            <th class="p-3">Time</th>
                            <th class="p-3">Classroom</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($timetable_data as $row) : ?>
                            <tr>
                                <td class="p-3"><?= htmlspecialchars($row['day']); ?></td>
                                <td class="p-3"><?= htmlspecialchars($row['subject']); ?></td>
                                <td class="p-3"><?= htmlspecialchars($row['start_time']); ?> - <?= htmlspecialchars($row['end_time'])?></td>
                                <td class="p-3"><?= htmlspecialchars($row['classroom']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Timetable rows dynamically inserted here -->
                       
                    </tbody>
                </table>
            </section>
        
        </div>
    </div>
</body>
</html>