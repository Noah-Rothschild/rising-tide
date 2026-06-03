<?php
session_start();

include('../config/db.php');

$seller_id = $_SESSION['user_id'];

$name = $_POST['name']; 
$description = $_POST['description'];
$price = $_POST['price'];
$category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;

$imageName = null;

if (!empty($_FILES['image']['name'])) {
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $target = "../assets/images/uploads/" . $imageName;

    move_uploaded_file($_FILES['image']['tmp_name'], $target);
}

$stmt = $conn->prepare("
    INSERT INTO products 
    (seller_id, category_id, name, description, price, image)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissds",
    $seller_id,
    $category_id,
    $name,
    $description,
    $price,
    $imageName
);

if ($stmt->execute()) {
    header("Location: list.php");
    exit();
} else {
    echo "Error creating product: " . $stmt->error;
}
?>