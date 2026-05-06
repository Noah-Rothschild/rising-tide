<?php
$conn = new mysqli("localhost", "root", "", "rising_tide");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>