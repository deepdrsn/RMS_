<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch teacher's schedule or other relevant data
$teacher_id = $_SESSION['teacher_id'];
$stmt = $conn->prepare("SELECT * FROM schedules WHERE teacher_id = :teacher_id");
$stmt->execute(['teacher_id' => $teacher_id]);
$schedules = $stmt->fetchAll();
?>


<?php
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher's schedule or other relevant data
$stmt = $conn->prepare("SELECT * FROM schedules WHERE teacher_id = :teacher_id");
$stmt->execute(['teacher_id' => $teacher_id]);
$schedules = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Welcome to Your Dashboard</h1>
    <h2>Your Schedule</h2>
    <table>
    <thead>
        <tr>
            <th>Time Slot</th>
            <th>Subject</th>
            <th>Class</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($schedules as $schedule): ?>
            <tr>
                <td><?php echo htmlspecialchars($schedule['time_slot']); ?></td>
                <td><?php echo htmlspecialchars($schedule['subject']); ?></td>
                <td><?php echo htmlspecialchars($schedule['class']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    <a href="logout.php">Logout</a>
</body>
</html>