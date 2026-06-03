<?php
session_start();
include('../includes/header.php');
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

$buyer_id = $_SESSION['user_id'];
$shipping_address = $_POST['shipping_address'] ?? '';
$city = $_POST['city'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$total_amount = floatval($_POST['total_amount'] ?? 0);

if (empty($shipping_address) || empty($city) || empty($postal_code)) {
    echo "Error: Please fill in all shipping details.";
    exit();
}

// Get user's cart
$cartStmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$cartStmt->bind_param("i", $buyer_id);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();
$cart = $cartResult->fetch_assoc();

if (!$cart) {
    echo "Error: No cart found.";
    exit();
}

$cart_id = $cart['id'];

// Get cart items with seller info
$itemsStmt = $conn->prepare("
    SELECT ci.product_id, ci.quantity, p.price, p.seller_id 
    FROM cart_items ci 
    JOIN products p ON ci.product_id = p.id 
    WHERE ci.cart_id = ?
");
$itemsStmt->bind_param("i", $cart_id);
$itemsStmt->execute();
$itemsResult = $itemsStmt->get_result();
$cartItems = [];

while ($row = $itemsResult->fetch_assoc()) {
    $cartItems[] = $row;
}

if (empty($cartItems)) {
    echo "Error: Cart is empty.";
    exit();
}

// Create order
$orderStmt = $conn->prepare("
    INSERT INTO orders (buyer_id, total_amount, status, payment_status, shipping_address, city, postal_code, created_at)
    VALUES (?, ?, 'pending', 'completed', ?, ?, ?, NOW())
");
$orderStmt->bind_param("idsss", $buyer_id, $total_amount, $shipping_address, $city, $postal_code);

if (!$orderStmt->execute()) {
    echo "Error creating order: " . $orderStmt->error;
    exit();
}

$order_id = $conn->insert_id;

// Create order items
$orderItemStmt = $conn->prepare("
    INSERT INTO order_items (order_id, product_id, quantity, price, seller_id)
    VALUES (?, ?, ?, ?, ?)
");

foreach ($cartItems as $item) {
    $product_id = intval($item['product_id']);
    $quantity = intval($item['quantity']);
    $price = floatval($item['price']);
    $seller_id = intval($item['seller_id']);
    
    $orderItemStmt->bind_param("iiidi", $order_id, $product_id, $quantity, $price, $seller_id);
    
    if (!$orderItemStmt->execute()) {
        echo "Error creating order item: " . $orderItemStmt->error;
        exit();
    }
}

// Clear cart
$deleteStmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
$deleteStmt->bind_param("i", $cart_id);
$deleteStmt->execute();

?>

<div class="cart-page">
    <header class="cart-header">
        <h1>Order Confirmed</h1>
        <p>Payment successful!</p>
    </header>

    <div class="cart-empty">
        <h2>✓ Order #<?php echo $order_id; ?> Placed Successfully</h2>
        <p>Thank you for your purchase! We'll contact you shortly with shipping details.</p>
        
        <div style="background: #f3f4f6; padding: 20px; border-radius: 10px; margin: 30px 0; text-align: left; max-width: 500px; margin-left: auto; margin-right: auto;">
            <p><strong>Shipping to:</strong><br>
            <?php echo htmlspecialchars($shipping_address); ?><br>
            <?php echo htmlspecialchars($city); ?>, <?php echo htmlspecialchars($postal_code); ?></p>
            
            <p><strong>Total Amount:</strong> R <?php echo number_format($total_amount, 2); ?></p>
            <p><strong>Status:</strong> Pending</p>
        </div>
        
        <a class="btn btn-primary" href="marketplace.php">Continue Shopping</a>
        <a class="btn btn-secondary" href="../user/dashboard.php">View Orders</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
