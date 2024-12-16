<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];


    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = :student_id");
    $stmt->execute(['student_id' => $student_id]);
    $student = $stmt->fetch();

    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['id'];
        header("Location: student.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<!-- <a href="index.php" class="btn">Back to Home</a> -->
    <form method="POST" action="">
        <h1>Student Login</h1>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <label for="student_id">Student ID:</label>
        <input type="text" name="student_id" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
