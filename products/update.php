<?php
session_start();

include('../config/db.php');

$seller_id = $_SESSION['user_id'];
$product_id = $_POST['id'];

$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
$category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;

$stmt = $conn->prepare("SELECT image FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $seller_id);
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
    $imageName = time() . '_' . basename($_FILES['image']['name']);
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
    SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ?
    WHERE id = ? AND seller_id = ?
");

$updateStmt->bind_param(
    "ssdissii",
    $name,
    $description,
    $price,
    $stock,
    $category_id,
    $imageName,
    $product_id,
    $seller_id
);

if ($updateStmt->execute()) {
    header("Location: ../user/dashboard.php");
    exit();
} else {
    echo "Error updating product.";
}
?>