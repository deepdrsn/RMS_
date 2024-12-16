<?php
include 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL statement to insert the teacher
    $stmt = $conn->prepare("INSERT INTO teachers (name, password) VALUES (:name, :password)");
    $stmt->execute(['name' => $name, 'password' => $hashed_password]);

    // Redirect back to the admin panel
    header("Location: admin.php");
    exit();
}
?>


