<?php 
include 'db.php';
session_start();

// Check if the schedule ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

// Fetch the schedule details
$stmt = $conn->prepare("SELECT * FROM routines WHERE id = :id");
$stmt->execute(['id' => $_GET['id']]);
$schedule = $stmt->fetch();

if (!$schedule) {
    header("Location: admin.php");
    exit();
}

// Fetch teachers and students for the dropdowns
$teachers = $conn->query("SELECT * FROM teachers")->fetchAll();
$students = $conn->query("SELECT * FROM students")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Schedule</title>
</head>
<body>
    <h1>Edit Schedule</h1>
    <form method="POST" action="update_schedule.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($schedule['id']); ?>">
        <input type="text" name="time_slot" placeholder="Time Slot" value="<?php echo htmlspecialchars($schedule['time_slot']); ?>" required>
        <select name="teacher_id" required>
            <option value="">Select Teacher</option>
            <?php foreach ($teachers as $teacher): ?>
                <option value="<?php echo htmlspecialchars($teacher['id']); ?>" <?php echo $teacher['id'] == $schedule['teacher_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($teacher['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="student_id" required>
            <option value="">Select Student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?php echo htmlspecialchars($student['id']); ?>" <?php echo $student['id'] == $schedule['student_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($student['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Update Schedule</button>
    </form>
</body>
</html>
