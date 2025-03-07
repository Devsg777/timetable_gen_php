<?php
include_once "../../config/database.php";
include_once "../../models/Student.php";

$database = new Database();
$conn = $database->getConnection();

// Fetch combinations for dropdown
$query = "SELECT * FROM combinations";
$stmt = $conn->prepare($query);
$stmt->execute();
$combinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "header.php"; ?>
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Add Student</h2>

    <form action="../../controllers/StudentController.php" method="POST" class="bg-white p-4 shadow rounded">
        <label>Name:</label>
        <input type="text" name="name" class="border p-2 w-full mb-2" required>

        <label>Email:</label>
        <input type="email" name="email" class="border p-2 w-full mb-2" required>

        <label>Phone:</label>
        <input type="text" name="phone_no" class="border p-2 w-full mb-2" required>

        <label>Address:</label>
        <input type="text" name="address" class="border p-2 w-full mb-2" required>

        <label>Password:</label>
        <input type="password" name="password" class="border p-2 w-full mb-2" required>
         

        <label>Combination:</label>
        <select name="combination_id" class="border p-2 w-full mb-2" required>
            <?php foreach ($combinations as $c): ?>
                <option value="<?= $c['id'] ?>"> <?= $c['semester'] ?> sem - <?= $c['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="add_student" class="bg-blue-500 text-white px-4 py-2 rounded">Add</button>
    </form>
<?php include "footer.php"; ?>
