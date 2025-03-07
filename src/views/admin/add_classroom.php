<?php include "header.php"; ?>
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Add Classroom</h2>
        <form action="../../controllers/classroomController.php" method="POST">
            <label class="block mb-2 font-semibold">Room No:</label>
            <input type="text" name="room_no" required class="w-full p-2 border rounded mb-3">

            <label class="block mb-2 font-semibold">Type:</label>
            <select name="type" class="w-full p-2 border rounded mb-3">
                <option value="theory">Theory</option>
                <option value="lab">Lab</option>
            </select>

            <button type="submit" name="add" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md">Add Classroom</button>
        </form>
    </div>
<?php include "footer.php"; ?>
