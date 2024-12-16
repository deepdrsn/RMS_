<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['id_number'];
    $name = $_POST['name'];
    $semester_id = $_POST['semester_id']; // Get semester ID
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO students (student_id, name, semester_id, password) VALUES (:student_id, :name, :semester_id, :password)");
    $stmt->execute([
        'student_id' => $student_id,
        'name' => $name,
        'semester_id' => $semester_id,
        'password' => $password
    ]);

    header("Location: admin.php");
    exit();
}
?>