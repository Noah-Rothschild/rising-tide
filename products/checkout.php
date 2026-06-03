<?php
include('../includes/header.php');
include('../config/db.php');

$orderComplete = false;
$items = [];
$total = 0.00;

if (!isset($_SESSION['user_id'])) {
    $cart = [];
} else {
    $user_id = $_SESSION['user_id'];
    $cartStmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $cartStmt->bind_param("i", $user_id);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();
    $cart = $cartResult->fetch_assoc();

    if ($cart) {
        $cart_id = $cart['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deleteItems = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $deleteItems->bind_param("i", $cart_id);
            $deleteItems->execute();
            $orderComplete = true;
        }

        $itemsStmt = $conn->prepare("SELECT ci.quantity, p.id, p.name, p.price, p.image FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = ?");
        $itemsStmt->bind_param("i", $cart_id);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();

        while ($row = $itemsResult->fetch_assoc()) {
            $quantity = max(1, intval($row['quantity']));
            $subtotal = floatval($row['price']) * $quantity;
            $total += $subtotal;

            $items[] = [
                'id' => intval($row['id']),
                'name' => $row['name'],
                'price' => floatval($row['price']),
                'image' => $row['image'],
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }
    }
}
?>

<div class="cart-page">
    <header class="cart-header">
        <h1>Checkout</h1>
        <p>Confirm your order details and complete checkout.</p>
    </header>

    <?php if ($orderComplete): ?>
        <div class="cart-empty">
            <h2>Order placed</h2>
            <p>Thank you! Your purchase has been recorded. We will contact you with the delivery details shortly.</p>
            <a class="btn btn-primary" href="marketplace.php">Shop again</a>
        </div>
    <?php elseif (empty($cart)): ?>
        <div class="cart-empty">
            <p>Your cart is empty. Add items before checking out.</p>
            <a class="btn btn-primary" href="marketplace.php">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-wrapper">
            <section class="cart-items">
                <?php foreach ($cart as $item): ?>
                    <article class="cart-item">
                        <img class="cart-item-image" src="../assets/images/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="cart-item-details">
                            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                            <p class="cart-item-price">R <?php echo number_format($item['price'], 2); ?></p>
                            <p class="cart-item-quantity">Quantity: <?php echo intval($item['quantity']); ?></p>
                            <p class="cart-item-subtotal">Subtotal: R <?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

            <aside class="cart-summary">
                <div class="cart-summary-box">
                    <h2>Order summary</h2>
                    <div class="cart-summary-row">
                        <span>Items total</span>
                        <strong>R <?php echo number_format($total, 2); ?></strong>
                    </div>

                    <div class="cart-summary-row">
                        <span>Shipping</span>
                        <strong>Free</strong>
                    </div>

                    <div class="cart-summary-total">
                        <span>Total</span>
                        <strong>R <?php echo number_format($total, 2); ?></strong>
                    </div>

                    <form method="POST" action="checkout.php">
                        <button type="submit" class="btn btn-primary btn-full">Confirm Purchase</button>
                    </form>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
