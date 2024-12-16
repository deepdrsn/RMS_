<?php
include 'db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: main.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the semester_id for the logged-in student

$stmt = $conn->prepare("SELECT semester_id FROM students WHERE id = :student_id");
$stmt->execute(['student_id' => $student_id]);
$student = $stmt->fetch();

if ($student) {
    $semester_id = $student['semester_id'];
} else {
    echo "Student not found.";
    exit();
}

// Fetch routines for the logged-in student
$stmt = $conn->prepare("
    SELECT s.time_slot, s.subject, t.name AS teacher_name 
    FROM schedules s 
    JOIN teachers t ON s.teacher_id = t.id 
    WHERE s.semester_id = :semester_id
");
$stmt->execute(['semester_id' => $semester_id]); 
$schedules = $stmt->fetchAll();

// For Debug output
// if (empty($routines)) {
//     echo "No routines found for this student.";
// } else {
//     echo "<pre>";
//     print_r($routines);
//     echo "</pre>";
// }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<!-- <a href="index.php" class="btn">Back to Home</a> -->

    <h1>Welcome to Your Dashboard</h1>
    <h2>Your Routine</h2>
    <table>
        <thead>
            <tr>
                <th>Time Slot</th>
                <th>Subject</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedules as $routine): ?>
                <tr>
                    <td><?php echo htmlspecialchars($routine['time_slot']); ?></td>
                    <td><?php echo htmlspecialchars($routine['subject']); ?></td>
                    <td><?php echo htmlspecialchars($routine['teacher_name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="logout.php">Logout</a>
</body>
</html>
