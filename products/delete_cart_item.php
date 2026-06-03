<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id <= 0) {
    header('Location: cart.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$cartStmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$cartStmt->bind_param("i", $user_id);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();
$cart = $cartResult->fetch_assoc();

if (!$cart) {
    header('Location: cart.php');
    exit();
}

$cart_id = $cart['id'];

$deleteStmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
$deleteStmt->bind_param("ii", $cart_id, $product_id);
$deleteStmt->execute();

header('Location: cart.php');
exit();
?>
