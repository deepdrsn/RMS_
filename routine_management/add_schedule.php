<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $time_slot = $_POST['time_slot'];
    $teacher_id = $_POST['teacher_id'];
    $semester_id = $_POST['semester_id'];
    $subject = $_POST['subject'];

    // Check for empty values
    if (empty($time_slot) || empty($teacher_id) || empty($semester_id) || empty($subject)) {
        echo "One or more fields are empty.";
        exit();
    }

    // Check if a schedule already exists for the same time slot and semester
    $stmt = $conn->prepare("SELECT COUNT(*) FROM schedules WHERE time_slot = :time_slot AND semester_id = :semester_id");
    $stmt->execute(['time_slot' => $time_slot, 'semester_id' => $semester_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "A schedule already exists for this time slot in the selected semester.";
        exit();
    }

    // Prepare the SQL statement to insert the new schedule
    $stmt = $conn->prepare("INSERT INTO schedules (time_slot, teacher_id, semester_id, subject) VALUES (:time_slot, :teacher_id, :semester_id, :subject)");

    // Execute the statement with the parameters
    try {
        $stmt->execute([
            'time_slot' => $time_slot,
            'teacher_id' => $teacher_id,
            'semester_id' => $semester_id,
            'subject' => $subject
        ]);
        // Redirect or provide feedback
        header("Location: admin.php");
        exit();
    } catch (PDOException $e) {
        // Output error message
        echo "Error: " . $e->getMessage();
    }
}
?>