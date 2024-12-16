<?php 
include 'db.php';
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $id = $_POST['id'];
    $time_slot = $_POST['time_slot'];
    $teacher_id = $_POST['teacher_id'];
    $student_id = $_POST['student_id'];

    // Validate inputs
    if (empty($id) || empty($time_slot) || empty($teacher_id) || empty($student_id)) {
        echo "All fields are required.";
        exit();
    }

    // Check if the schedule already exists for the same time slot and student
    $stmt = $conn->prepare("SELECT COUNT(*) FROM routines WHERE time_slot = :time_slot AND student_id = :student_id AND id != :id");
    $stmt->execute([
        'time_slot' => $time_slot,
        'student_id' => $student_id,
        'id' => $id
    ]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "A schedule already exists for this time slot and student.";
        exit();
    }

    // Update the schedule in the database
    try {
        $stmt = $conn->prepare("
            UPDATE routines 
            SET time_slot = :time_slot, teacher_id = :teacher_id, student_id = :student_id
            WHERE id = :id
        ");
        $stmt->execute([
            'time_slot' => $time_slot,
            'teacher_id' => $teacher_id,
            'student_id' => $student_id,
            'id' => $id
        ]);

        // Redirect to the admin page or show a success message
        header("Location: admin.php?success=1");
        exit();
    } catch (PDOException $e) {
        // Output error message
        echo "Error updating the schedule: " . $e->getMessage();
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
