<?php
include('../config/db.php');

$username = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = 'seller';

$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $password, $role);

if ($stmt->execute()) {
    echo "Seller registration successful!";
    header("Location: ../auth/login.php");
} else {
    echo "Error: " . $stmt->error;
}
?>
?>
