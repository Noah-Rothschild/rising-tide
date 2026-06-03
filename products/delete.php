<?php
session_start();

include('../config/db.php');

if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$product_id = $_GET['id'];

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT * FROM products
    WHERE id = ? AND seller_id = ?
");

$stmt->bind_param("ii", $product_id, $user_id);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found or unauthorized.");
}


$imagePath = "../assets/images/uploads/" . $product['image'];

if (file_exists($imagePath)) {
    unlink($imagePath);
}

$deleteStmt = $conn->prepare("
    DELETE FROM products
    WHERE id = ?
");

$deleteStmt->bind_param("i", $product_id);

if ($deleteStmt->execute()) {

    header("Location: ../user/dashboard.php");

    exit();

} else {

    echo "Error deleting product.";
}
?>