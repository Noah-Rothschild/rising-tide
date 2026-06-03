<?php
include('../includes/header.php');
include('../config/db.php');

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
        $itemsStmt = $conn->prepare("SELECT ci.quantity, ci.product_id, p.id, p.name, p.price, p.image FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = ?");
        $itemsStmt->bind_param("i", $cart_id);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();

        while ($row = $itemsResult->fetch_assoc()) {
            $quantity = max(1, intval($row['quantity']));
            $subtotal = floatval($row['price']) * $quantity;
            $total += $subtotal;

            $items[] = [
                'id' => intval($row['id']),
                'product_id' => intval($row['product_id']),
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
        <h1>Shopping Cart</h1>
        <p>Your selected items are listed below. Update quantities from the product page.</p>
    </header>

    <?php if (empty($items)): ?>
        <div class="cart-empty">
            <p>Your cart is empty.</p>
            <a class="btn btn-primary" href="marketplace.php">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-wrapper">
            <section class="cart-items">
                <?php foreach ($items as $item): ?>
                    <article class="cart-item">
                        <img
                            class="cart-item-image"
                            src="../assets/images/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                        >

                        <div class="cart-item-details">
                            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                            <p class="cart-item-price">R <?php echo number_format($item['price'], 2); ?></p>
                            <p class="cart-item-quantity">Quantity: <?php echo intval($item['quantity']); ?></p>
                            <p class="cart-item-subtotal">Subtotal: R <?php echo number_format($item['subtotal'], 2); ?></p>
                            <div class="cart-item-actions">
                                <a class="btn btn-secondary" href="view.php?id=<?php echo intval($item['id']); ?>">Edit item</a>
                                <form method="POST" action="delete_cart_item.php" style="flex: 1;">
                                    <input type="hidden" name="product_id" value="<?php echo intval($item['product_id']); ?>">
                                    <button type="submit" class="btn btn-danger" style="width: 100%;">Remove</button>
                                </form>
                            </div>
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

                    <a class="btn btn-primary btn-full" href="checkout.php">Proceed to Checkout</a>
                </div>
            </aside>
        </div>
    <?php endif; ?>

</div>

<?php include('../includes/footer.php'); ?>
