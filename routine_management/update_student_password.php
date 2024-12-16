<?php
include 'db.php'; // Include the database connection
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $new_password = $_POST['new_password'];

    // Check for empty fields
    if (empty($student_id) || empty($new_password)) {
        echo "Student ID and new password are required.";
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    try {
        $stmt = $conn->prepare("UPDATE students SET password = :password WHERE id = :id");
        $stmt->execute([
            'password' => $hashed_password,
            'id' => $student_id
        ]);
        header("Location: admin.php?success=student_password_updated");
        exit();
    } catch (PDOException $e) {
        echo "Error updating student password: " . $e->getMessage();
        exit();
    }
} else {
    echo "Invalid request method.";
    exit();
}
?>
