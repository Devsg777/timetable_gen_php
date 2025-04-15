<?php
include_once __DIR__ . '/../../config/database.php';
include_once "../../models/Student.php";

$database = new Database();
$db = $database->getConnection();

// Initialize Admin Model
$student = new Student($db);


// Check Login Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($student->login($email, $password)) {
        echo "Hello";
        header("Location: dashboard.php"); // Redirect to Dashboard
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold text-center">Student Login</h2>
        <?php if (isset($error)) { echo "<p class='text-red-500'>$error</p>"; } ?>
        <form method="POST" class="mt-4" >
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded mb-2">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 border rounded mb-2">
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-700">Login</button>
        </form>
        GO back to <a class="" href="../../../index.php">Home</a>
    </div>
</body>
</html>
