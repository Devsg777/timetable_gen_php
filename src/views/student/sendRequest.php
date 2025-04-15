<?php
// Include necessary files and initialize session
require_once './header.php';
require_once '../../config/database.php';

// Check if the form is submitted
// $entryId = $_GET['id'];
// $sub = $_GET['sub'];
// $room = $_GET['rn'];
// $s_time = $_GET['st'];
// $e_time = $_GET['et'];
// $day = $_GET['day'];

$entryId = 100;
$sub = "History";
$room = 2;
$s_time = '10:00';
$e_time = '11:00';
$day = 'Tuesday';

?>
    <div class="container mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">Send Schedule Change Request</h2>
        <form action="../../controllers/requestController.php" method="POST" class="bg-white shadow-md rounded p-4">
            <label class="block mb-2">Request Type:</label>
            <select name="request_type" required class="w-full p-2 border rounded">
                <option value="Change Class">Reschedule</option>
                <option value="Change Time">Cancle class</option>
                <option value="Other">Other</option>
            </select>
            <label class="block mb-2">Day:</label>
            <select name="day" required class="w-full p-2 border rounded">
            <option value="Mondaya" <?= $day == 'Monday' ? 'selected' : ''; ?>>Monday</option>
            <option value="Tuesday" <?= $day == 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
            </select>
            <label class="block mb-2">Time:</label>
            <select name="time" required class="w-full p-2 border rounded">
                <option value="Change Class">Reschedule</option>
                <option value="Change Time">Cancle class</option>
                <option value="Other">Other</option>
            </select>

            <label class="block mt-2">Description:</label>
            <textarea name="description" required class="w-full p-2 border rounded"></textarea>

            <button type="submit" name="send_request" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Send Request</button>
        </form>
    </div>
 </div>   
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>