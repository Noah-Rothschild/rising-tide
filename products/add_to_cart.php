<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view.php');
    exit();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in first']);
    exit();
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

$stmt = $conn->prepare("SELECT id, name, price, image, stock FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

if ($product['stock'] !== null) {
    $quantity = min($quantity, intval($product['stock']));
}

if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Out of stock']);
    exit();
}

$user_id = $_SESSION['user_id'];

$cartStmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
if (!$cartStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
}
$cartStmt->bind_param("i", $user_id);
$cartStmt->execute();
if ($cartStmt->error) {
    echo json_encode(['success' => false, 'message' => 'Cart select error: ' . $cartStmt->error]);
    exit();
}
$cartResult = $cartStmt->get_result();
$cart = $cartResult->fetch_assoc();

if ($cart) {
    $cart_id = $cart['id'];
} else {
    $createCart = $conn->prepare("INSERT INTO carts (user_id, created_at) VALUES (?, NOW())");
    if (!$createCart) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }
    $createCart->bind_param("i", $user_id);
    if (!$createCart->execute()) {
        echo json_encode(['success' => false, 'message' => 'Insert cart error: ' . $createCart->error]);
        exit();
    }
    $cart_id = $conn->insert_id;
}

$itemStmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
if (!$itemStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
}
$itemStmt->bind_param("ii", $cart_id, $product_id);
$itemStmt->execute();
if ($itemStmt->error) {
    echo json_encode(['success' => false, 'message' => 'Cart item select error: ' . $itemStmt->error]);
    exit();
}
$itemResult = $itemStmt->get_result();
$cartItem = $itemResult->fetch_assoc();

if ($cartItem) {
    $newQuantity = intval($cartItem['quantity']) + $quantity;
    $updateItem = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    if (!$updateItem) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }
    $updateItem->bind_param("ii", $newQuantity, $cartItem['id']);
    if (!$updateItem->execute()) {
        echo json_encode(['success' => false, 'message' => 'Update cart item error: ' . $updateItem->error]);
        exit();
    }
} else {
    $insertItem = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    if (!$insertItem) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }
    $insertItem->bind_param("iii", $cart_id, $product_id, $quantity);
    if (!$insertItem->execute()) {
        echo json_encode(['success' => false, 'message' => 'Insert cart item error: ' . $insertItem->error]);
        exit();
    }
}

echo json_encode(['success' => true, 'message' => 'Added to cart']);
exit();
