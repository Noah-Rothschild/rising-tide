<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /rising-tide/auth/login.php");
    exit();
}
?>