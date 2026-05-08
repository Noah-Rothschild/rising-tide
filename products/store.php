<?php
session_start();

include('../config/db.php');

$user_id = $_SESSION['user_id'];

$title = $_POST['title'];
$description = $_POST['description'];
$price = $_POST['price'];

$imageName = time() . '_' . $_FILES['image']['name'];
$target = "../assets/images/uploads/" . $imageName;

move_uploaded_file($_FILES['image']['tmp_name'], $target);

$stmt = $conn->prepare("
    INSERT INTO products 
    (user_id, title, description, price, image)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "issds",
    $user_id,
    $title,
    $description,
    $price,
    $imageName
);

if ($stmt->execute()) {
    header("Location: list.php");
    exit();
} else {
    echo "Error creating product.";
}
?>