<?php include "header.php"; ?>
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Add Combination</h2>
    <form action="../../controllers/combinationController.php" method="POST" class="bg-white shadow-md rounded p-4">
        <label class="block mb-2">Name:</label>
        <input type="text" name="name" required class="w-full p-2 border rounded">

        <label class="block mt-2">Department:</label>
        <input type="text" name="department" required class="w-full p-2 border rounded">

        <label class="block mt-2">Semester:</label>
        <input type="number" name="semester" required class="w-full p-2 border rounded">

        <button type="submit" name="add" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Add</button>
    </form>
</div>
<?php include "footer.php"; ?>
