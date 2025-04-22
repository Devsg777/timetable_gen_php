<?php include "./header.php";
    include_once "../../config/database.php";
    include_once "../../models/Student.php";
    include_once "../../models/Combination.php";

    $s_id = $_SESSION['student_id'];
    $success_msg = $error_msg = "";
    $database = new Database();
    $db = $database->getConnection();
    $stu = new Student($db);
    $combination = new Combination($db);

    $combinations = $combination->getCombinations();

    if (!$s_id) {
        header("Location:login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update_profile'])) {
            $name = $_POST['name'];
            $email = $_SESSION['student_email'];
            $combination_id = $_POST['combination_id'];
            $phone_no = $_POST['phone_no'];
            $address = $_POST['address'];

            // Assuming you have an updateStudent method in your Student model
            if ($stu->updateStudent($s_id, $name,$email, $combination_id, $phone_no, $address)) {
                $success_msg = "Profile updated successfully!";
                // Refresh student data after update
                $student = $stu->getStudentById($s_id);
            } else {
                $error_msg = "Error updating profile.";
            }
        } elseif (isset($_POST['change_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Fetch stored password hash
            $password_sql = "SELECT password FROM students WHERE id = :id";
            $password_stmt = $db->prepare($password_sql);
            $password_stmt->bindParam(':id', $s_id);
            $password_stmt->execute();
            $password_stmt->bindColumn(1, $hashed_password);
            $password_stmt->fetch();
            $password_stmt->closeCursor();

            if (!password_verify($current_password, $hashed_password)) {
                $error_msg = "Current password is incorrect!";
            } elseif ($new_password !== $confirm_password) {
                $error_msg = "New passwords do not match!";
            } else {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password_sql = "UPDATE students SET password = :password WHERE id = :id";
                $update_password_stmt = $db->prepare($update_password_sql);
                $update_password_stmt->bindParam(':password', $new_hashed_password);
                $update_password_stmt->bindParam(':id', $s_id);

                if ($update_password_stmt->execute()) {
                    $success_msg = "Password changed successfully!";
                } else {
                    $error_msg = "Error changing password!";
                }
            }
        }
    }

    // Fetch current student data again after potential updates
    $currentStudent = $stu->getStudentById($s_id);
?>
    <div class="max-w-3xl mx-auto bg-white p-8 shadow-lg rounded-lg">
        <h2 class="text-3xl font-bold text-blue-700 mb-6">Edit Your Profile</h2>

        <?php if ($success_msg): ?>
            <div class="bg-green-200 text-green-700 p-3 rounded mb-4"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="bg-red-200 text-red-700 p-3 rounded mb-4"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($currentStudent['name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="combination_id" class="block  text-sm font-bold mb-2">Combination:</label>
                        <select id="combination_id" name="combination_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <?php foreach ($combinations as $comb) { ?>
                                <option value="<?= $comb['id']; ?>" <?= $currentStudent['combination_id'] == $comb['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($comb['semester']); ?> sem - <?= htmlspecialchars($comb['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($currentStudent['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100" disabled>
                        <p class="text-gray-500 text-xs italic">Email cannot be changed.</p>
                    </div>
                    <div>
                        <label for="phone_no" class="block text-gray-700 text-sm font-bold mb-2">Phone No:</label>
                        <input type="text" id="phone_no" name="phone_no" value="<?php echo htmlspecialchars($currentStudent['phone_no']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address:</label>
                        <textarea id="address" name="address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($currentStudent['address']); ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Profile</button>
                </form>
            </div>

            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Change Password</h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-gray-700 text-sm font-bold mb-2">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">New Password:</label>
                        <input type="password" id="new_password" name="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <button type="submit" name="change_password" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>