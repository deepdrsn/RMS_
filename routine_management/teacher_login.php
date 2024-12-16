<?php
session_start();
include 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']); // Sanitize username
    $password = $_POST['password'];

    // Query to check if the teacher exists
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $teacher = $stmt->fetch();

    // Verify password
    if ($teacher && password_verify($password, $teacher['password'])) {
        $_SESSION['teacher_id'] = $teacher['id'];
        header("Location: teacher_dashboard.php"); // Redirect to teacher dashboard
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Teacher Login</h1>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>