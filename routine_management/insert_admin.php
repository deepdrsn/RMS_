<?php
include 'db.php';

$username = 'admin';
$password = password_hash('admin', PASSWORD_DEFAULT); 

$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
$stmt->execute(['username' => $username, 'password' => $password]);

echo "Admin user created successfully.";
?>
