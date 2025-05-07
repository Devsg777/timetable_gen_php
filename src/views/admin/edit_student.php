<?php
include_once '../../config/database.php';
include_once '../../models/Student.php';
include_once '../../models/Combination.php';

$database = new Database();
$db = $database->getConnection();
$student = new Student($db);
$combination = new Combination($db);

$combinations = $combination->getCombinations();

if (!isset($_GET['id'])) {
    header("Location: students.php?error=Invalid Student ID");
    exit;
}

$id = $_GET['id'];
$currentStudent = $student->getStudentById($id);

if (!$currentStudent) {
    header("Location: students.php?error=Student not found");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];
    $address = $_POST['address'];
    $combination_id = $_POST['combination_id'];
    $section = $_POST['section'];
    $password = $_POST['password'];

    if ($combination_id == '') {
        header("Location: students.php?error=Please select a combination");
        exit;
    }

    if ($password != '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $student->updateStudentWithPass($id, $name, $email, $phone_no, $address, $combination_id, $section,$hashedPassword );
    } else {
        $student->updateStudent($id, $name, $email, $phone_no, $address, $combination_id, $section);
    }
    header("Location: students.php?success=Student updated successfully");
    exit(); // Ensure script stops after redirection
}

?>

<?php include "header.php"; ?>

    <div class="max-w-lg bg-white p-6 rounded shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Edit Student</h2>

        <?php if (isset($_GET['error'])) { ?>
            <p class="bg-red-100 text-red-700 px-4 py-2 rounded"><?= $_GET['error']; ?></p>
        <?php } ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block font-medium">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($currentStudent['name']); ?>" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($currentStudent['email']); ?>" required class="w-full border px-3 py-2 rounded">
            </div>
            <div class="mb-4">
                <label class="block font-medium">Phone Number</label>
                <input type="text" name="phone_no" value="<?= htmlspecialchars($currentStudent['phone_no']); ?>" required class="w-full border px-3 py-2 rounded">
            </div>
            <div class="mb-4">
                <label class="block font-medium">Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($currentStudent['address']); ?>" required class="w-full border px-3 py-2 rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Combination</label>
                <select name="combination_id" class="w-full border rounded px-3 py-2">
                    <?php foreach ($combinations as $comb) { ?>
                        <option value="<?= $comb['id']; ?>" <?= ($currentStudent['combination_id'] == $comb['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($comb['semester']); ?> sem - <?= htmlspecialchars($comb['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label class="block font-medium">Section</label>
                <select name="section" class="w-full border rounded px-3 py-2">
                    <option value="A" <?= ($currentStudent['section'] == 'A') ? 'selected' : ''; ?>>A</option>
                    <option value="B" <?= ($currentStudent['section'] == 'B') ? 'selected' : ''; ?>>B</option>
                    <option value="C" <?= ($currentStudent['section'] == 'C') ? 'selected' : ''; ?>>C</option>
                    <option value="D" <?= ($currentStudent['section'] == 'D') ? 'selected' : ''; ?>>D</option>
                    <option value="E" <?= ($currentStudent['section'] == 'E') ? 'selected' : ''; ?>>E</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Password</label>
                <input type="password" name="password" class="w-full border px-3 py-2 rounded" placeholder="Leave blank to keep current password">
            </div>
            <div class="mb-4">
                <button type="submit" name="edit_student" class="bg-blue-500 text-white px-4 py-2 rounded">Update Student</button>
            </div>
        </form>
    </div>

<?php include "footer.php"; ?>