<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle delete operations
if (isset($_GET['delete_student_id'])) {
    $stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete_student_id']]);
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_teacher_id'])) {
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete_teacher_id']]);
    header("Location: admin.php");
    exit();
}
// Fetch semesters
$semesters = $conn->query("SELECT * FROM semesters")->fetchAll();

// Fetch students with their semesters
$students = $conn->query("
    SELECT s.*, sem.name AS semester_name 
    FROM students s 
    JOIN semesters sem ON s.semester_id = sem.id
")->fetchAll();

// Check if a semester_id is set for filtering
$semester_id = isset($_GET['semester_id']) ? $_GET['semester_id'] : null;

// Fetch existing schedules based on the selected semester
// Fetch existing schedules based on the selected semester
if ($semester_id) {
    $stmt = $conn->prepare("SELECT s.id, s.time_slot, t.name AS teacher_name, sem.name AS semester_name, s.subject 
    FROM schedules s 
    JOIN teachers t ON s.teacher_id = t.id 
    JOIN semesters sem ON s.semester_id = sem.id 
    WHERE s.semester_id = :semester_id");
    $stmt->execute(['semester_id' => $semester_id]); // Execute the statement
} else {
    // Fetch all schedules if no semester is selected
    $stmt = $conn->prepare("SELECT s.id, s.time_slot, t.name AS teacher_name, sem.name AS semester_name, s.subject 
    FROM schedules s 
    JOIN teachers t ON s.teacher_id = t.id 
    JOIN semesters sem ON s.semester_id = sem.id");
    $stmt->execute(); // Execute the statement
}

$schedules = $stmt->fetchAll(); // Fetch the results after executing the statement

// Fetch students and teachers
$students = $conn->query("SELECT * FROM students")->fetchAll();
$teachers = $conn->query("SELECT * FROM teachers")->fetchAll();


// Fetch existing schedules
$stmt = $conn->prepare("SELECT s.id, s.time_slot, t.name AS teacher_name, sem.name AS semester_name, s.subject 
                         FROM schedules s 
                         JOIN teachers t ON s.teacher_id = t.id 
                         JOIN semesters sem ON s.semester_id = sem.id 
                         WHERE s.semester_id = :semester_id")->fetchAll();

// Handle delete operations for schedules
if (isset($_GET['delete_schedule_id'])) {
    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete_schedule_id']]);
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .btn {
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px;
            background: #007bff;
            color: white;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        h1, h2 {
            color: #333;
        }
        .form-inline input {
            padding: 5px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Admin Panel</h1>
    <!-- <a href="index.php" class="btn">Back to Home</a> -->
    <a href="logout.php" class="btn">Logout</a>

    <h2>Students</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Semester</th> <!-- New column for Semester -->
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($student['semester_id']); ?></td> <!-- Display Semester -->
                <td>
                    <a href="admin.php?delete_student_id=<?php echo $student['id']; ?>" 
                       class="btn" 
                       onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    <button onclick="openPasswordModal(<?php echo $student['id']; ?>)" class="btn">Change Password</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    
    <!-- ADDING NEW STUDENT -->

    <h2>Add New Student</h2>
<form method="POST" action="add_student.php">
    <input type="text" name="id_number" placeholder="Student ID" required>
    <input type="text" name="name" placeholder="Name" required>
    <select name="semester_id" required>
        <option value="">Select Semester</option>
        <?php
        // Fetch semesters from the database
        $semesters = $conn->query("SELECT * FROM semesters")->fetchAll();
        foreach ($semesters as $semester): ?>
            <option value="<?php echo htmlspecialchars($semester['id']); ?>">
                <?php echo htmlspecialchars($semester['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Add Student</button>
</form>
    <h2>Teachers</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?php echo htmlspecialchars($teacher['id']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                    <td>
                        <a href="admin.php?delete_teacher_id=<?php echo $teacher['id']; ?>" 
                           class="btn" 
                           onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

 <!--ADDING NEW TEACHER-->

 <h2>Add New Teacher</h2>
<form method="POST" action="add_teacher.php" class="form-inline">
    <input type="text" name="name" placeholder="Name" required>
    <input type="password" name="password" placeholder="Password" required> <!-- Password field -->
    <button type="submit" class="btn">Add Teacher</button>
</form>

<td>
    <a href="admin.php?delete_teacher_id=<?php echo $teacher['id']; ?>" 
       class="btn" 
       onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a>
    <button onclick="openPasswordModal(<?php echo $teacher['id']; ?>)" class="btn">Change Password</button>
</td>

    <!--  -->
    <!-- Filter form -->
    <h2>Filter Schedules by Semester</h2>
<form method="GET" action="admin.php">
    <select name="semester_id" required>
        <option value="">Select Semester</option>
        <?php foreach ($semesters as $semester): ?>
            <option value="<?php echo htmlspecialchars($semester['id']); ?>">
                <?php echo htmlspecialchars($semester['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filter</button>
</form>

    <!-- EXISTING SCHEDULES -->

     
    <h2>Existing Schedules</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Time Slot</th>
            <th>Teacher</th>
            <th>Semester</th>
            <th>Subject</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($schedules as $schedule): ?>
            <tr>
                <td><?php echo htmlspecialchars($schedule['id']); ?></td>
                <td><?php echo htmlspecialchars($schedule['time_slot']); ?></td>
                <td><?php echo htmlspecialchars($schedule['teacher_name']); ?></td>
                <td><?php echo htmlspecialchars($schedule['semester_name']); ?></td>
                <td><?php echo htmlspecialchars($schedule['subject']); ?></td>
                <td>
                    <a href="admin.php?delete_schedule_id=<?php echo $schedule['id']; ?>" 
                       class="btn" 
                       onclick="return confirm('Are you sure you want to delete this schedule?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


     <!--  -->
     
    <!--  -->
    <!--ADDING NEW SCHEDULE-->

<h2>Add New Schedule</h2>
<form method="POST" action="add_schedule.php">
    <input type="text" name="time_slot" placeholder="Time Slot (e.g., 10:00 AM - 11:00 AM)" required>
    <select name="teacher_id" required>
        <option value="">Select Teacher</option>
        <?php foreach ($teachers as $teacher): ?>
            <option value="<?php echo htmlspecialchars($teacher['id']); ?>">
                <?php echo htmlspecialchars($teacher['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="semester_id" required>
        <option value="">Select Semester</option>
        <?php foreach ($semesters as $semester): ?>
            <option value="<?php echo htmlspecialchars($semester['id']); ?>">
                <?php echo htmlspecialchars($semester['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="subject" placeholder="Subject" required>
    <button type="submit">Add Schedule</button>
</form>
    <!--  -->
    <!--  -->
    <!-- Modal for changing teacher password -->
<div id="passwordModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
    <h2>Change Teacher Password</h2>
    <form method="POST" action="update_teacher_password.php">
        <input type="hidden" name="teacher_id" id="modalTeacherId">
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit" class="btn">Update Password</button>
        <button type="button" onclick="closePasswordModal()" class="btn">Cancel</button>
    </form>
</div>

<script>
    function openPasswordModal(teacherId) {
        document.getElementById('modalTeacherId').value = teacherId;
        document.getElementById('passwordModal').style.display = 'block';
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').style.display = 'none';
    }
</script>

    <!-- Modal for changing password -->
    <div id="passwordModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
        <h2>Change Password</h2>
        <form method="POST" action="update_student_password.php">
            <input type="hidden" name="student_id" id="modalStudentId">
            <input type="password" name="new_password" placeholder="New Password" required>
            <button type="submit" class="btn">Update Password</button>
            <button type="button" onclick="closePasswordModal()" class="btn">Cancel</button>
        </form>
    </div>

    <script>
        function openPasswordModal(studentId) {
            document.getElementById('modalStudentId').value = studentId;
            document.getElementById('passwordModal').style.display = 'block';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }
    </script>
</body>
</html>
