<?php
session_start();

include('../config/db.php');

$user_id = $_SESSION['user_id'];
$product_id = $_POST['id'];

$title = $_POST['title'];
$description = $_POST['description'];
$price = $_POST['price'];

$stmt = $conn->prepare("SELECT image FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found or you don't have permission to edit it.";
    exit();
}

$current_image = $product['image'];

$imageName = $current_image; // Default to current image

if (!empty($_FILES['image']['name'])) {
    $imageName = time() . '_' . $_FILES['image']['name'];
    $target = "../assets/images/uploads/" . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Delete old image if new one uploaded successfully
        if ($current_image && file_exists("../assets/images/uploads/" . $current_image)) {
            unlink("../assets/images/uploads/" . $current_image);
        }
    } else {
        echo "Error uploading image.";
        exit();
    }
}

$updateStmt = $conn->prepare("
    UPDATE products
    SET title = ?, description = ?, price = ?, image = ?
    WHERE id = ? AND user_id = ?
");

$updateStmt->bind_param(
    "ssdssi",
    $title,
    $description,
    $price,
    $imageName,
    $product_id,
    $user_id
);

if ($updateStmt->execute()) {
    header("Location: list.php");
    exit();
} else {
    echo "Error updating product.";
}
?>