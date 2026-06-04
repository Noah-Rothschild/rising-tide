<?php
$conn = new mysqli("localhost", "root", "", "rising-tide");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>