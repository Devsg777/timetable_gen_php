
<?php include "header.php"; ?>
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Add Teacher</h2>


        <form method="POST" action="../../controllers/teacherController.php" >
            <div class="mb-4">
                <label class="block font-medium">Name</label>
                <input type="text" name="name" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Department</label>
                <input type="text" name="department" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Email</label>
                <input type="email" name="email" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Password</label>
                <input type="password" name="password" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Phone</label>
                <input type="text" name="phone_no" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Address</label>
                <textarea name="address" class="w-full border px-3 py-2 rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Min Classes/Week</label>
                <input type="number" name="min_class_hours_week" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Min Labs/Week</label>
                <input type="number" name="min_lab_hours_week" required class="w-full border px-3 py-2 rounded">
            </div>

            <button type="submit" name="add" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Teacher</button>
        </form>
    </div>
<?php include "footer.php"; ?>
