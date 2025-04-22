<?php
include_once __DIR__ . '/../../config/database.php';
include_once "../../models/Student.php";

// Start the session (ensure this is at the very top of your script)
session_start();

$database = new Database();
$db = $database->getConnection();

// Initialize Student Model
$student = new Student($db);

// Initialize error variable
$error = "";

// Check Login Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($student->login($email, $password)) {
        // Assuming your login function sets some session variable upon success
        // For example: $_SESSION['student_logged_in'] = true;
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
        <?php if ($error): ?>
            <p class='text-red-500'><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" class="mt-4" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded mb-2" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 border rounded mb-2">
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-700">Login</button>
        </form>
        Go back to <a class="" href="../../../index.php">Home</a>
    </div>
</body>
</html>