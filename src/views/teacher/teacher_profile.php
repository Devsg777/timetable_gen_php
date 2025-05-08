<?php include "./header.php";
    include_once "../../config/database.php";
    include_once "../../models/Teacher.php";

    $teacher_id = $_SESSION['teacher_id'];
    $success_msg = $error_msg = "";
    $database = new Database();
    $db = $database->getConnection();
    $teachers = new Teacher($db);

    $teacher = $teachers->getTeacherById($teacher_id);

    if(!$teacher){
        header("Location: ./../../index.php");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Fetch stored password hash
        $password_sql = "SELECT password FROM teachers WHERE id = ?";
        $password_stmt = $db->prepare($password_sql); // Corrected line
        $password_stmt->bindParam(1, $teacher_id, PDO::PARAM_INT); // Use PDO::bindParam
        $password_stmt->execute();
        $password_stmt->bindColumn(1, $hashed_password); // Use bindColumn
        $password_stmt->fetch(PDO::FETCH_BOUND); // Fetch using PDO::FETCH_BOUND
        $password_stmt->closeCursor(); // Use closeCursor

        if (!password_verify($current_password, $hashed_password)) {
            $error_msg = "Current password is incorrect!";
        } elseif ($new_password !== $confirm_password) {
            $error_msg = "New passwords do not match!";
        } else {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_sql = "UPDATE teachers SET password = ? WHERE id = ?";
            $update_password_stmt = $db->prepare($update_password_sql); // Corrected line
            $update_password_stmt->bindParam(1, $new_hashed_password, PDO::PARAM_STR); // Use PDO::bindParam
            $update_password_stmt->bindParam(2, $teacher_id, PDO::PARAM_INT); // Use PDO::bindParam

            if ($update_password_stmt->execute()) {
                $success_msg = "Password changed successfully!";
            } else {
                $error_msg = "Error changing password!";
            }
        }
    }
?>
        <div class="max-w-2xl mx-auto bg-white p-6 shadow-lg rounded-lg flex gap-6" >
            <div>
        <h2 class="text-2xl font-bold mb-4">Teacher Profile</h2>

        <?php if ($success_msg): ?>
            <p class="bg-green-200 text-green-700 p-2 rounded"><?php echo $success_msg; ?></p>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <p class="bg-red-200 text-red-700 p-2 rounded"><?php echo $error_msg; ?></p>
        <?php endif; ?>

        <form method="post" class="space-y-4" action="./../../controllers/teacherController.php">
    
            <label class="block font-semibold">Name:</label>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($teacher['id']); ?>">
            <input type="hidden" name="min_class_hours_week" value="<?php echo htmlspecialchars($teacher['min_class_hours_week']); ?>">
            <input type="hidden" name="min_lab_hours_week" value="<?php echo htmlspecialchars($teacher['min_lab_hours_week']); ?>">

            <input type="text" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" class="w-full p-2 border rounded">

            <label class="block font-semibold">Department:</label>
            <input type="text" name="department" value="<?php echo htmlspecialchars($teacher['department']); ?>" class="w-full p-2 border rounded bg-gray-200" disabled>

            <label class="block font-semibold">Email:</label>
            <input type="email" name="emial" value="<?php echo htmlspecialchars($teacher['email']); ?>" class="w-full p-2 border rounded bg-gray-200" disabled>

            <label class="block font-semibold">Phone No:</label>
            <input type="text" name="phone_no" value="<?php echo htmlspecialchars($teacher['phone_no']); ?>" class="w-full p-2 border rounded">

            <label class="block font-semibold">Address:</label>
            <textarea name="address" class="w-full p-2 border rounded"><?php echo htmlspecialchars($teacher['address']); ?></textarea>

            <button type="submit" name="edit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Profile</button>
        </form>
        </div>
        <div>

        <h2 class="text-2xl font-bold mb-4">Change Password</h2>
        <form method="POST" class="space-y-4">
            <label class="block font-semibold">Current Password:</label>
            <input type="password" name="current_password" class="w-full p-2 border rounded">

            <label class="block font-semibold">New Password:</label>
            <input type="password" name="new_password" class="w-full p-2 border rounded">

            <label class="block font-semibold">Confirm New Password:</label>
            <input type="password" name="confirm_password" class="w-full p-2 border rounded">

            <button type="submit" name="change_password" class="bg-red-600 text-white px-4 py-2 rounded">Change Password</button>
        </form>
        </div>
    </div>
    </div>

</body>
</html>