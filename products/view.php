<?php
include('../includes/header.php');
include('../config/db.php');

if (!isset($_GET['id'])) {
    die("Product not found.");
}

$product_id = (int)$_GET['id'];

$stmt = $conn->prepare("
    SELECT 
        products.*,
        users.username AS seller_name,
        users.email
    FROM products
    JOIN users
        ON products.seller_id = users.id
    WHERE products.id = ?
");

$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<div class="product-page">

    <div class="product-top-section">

        <div class="product-image-panel">
            <div class="product-image-frame">
                <img
                    src="../assets/images/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="product-main-image"
                >
            </div>
        </div>

        <div class="product-detail-panel">
            <div class="product-badge">Sponsored</div>

            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

            <div class="product-rating-row">
                <span class="product-rating">★★★★☆</span>
                <span class="product-review-count">(32 ratings)</span>
            </div>

            <div class="product-meta-row">
                <span><strong>Sold by:</strong> <?php echo htmlspecialchars($product['seller_name']); ?></span>
                <span><strong>Listed:</strong> <?php echo date("d M Y", strtotime($product['created_at'])); ?></span>
            </div>

            <?php
                $hasStock = $product['stock'] === null || $product['stock'] > 0;
                $stockLabel = $product['stock'] === null
                    ? 'Available'
                    : ($product['stock'] > 0 ? 'In stock' : 'Out of stock');
            ?>

            <div class="product-price-row">
                <span class="product-price">R <?php echo number_format($product['price'], 2); ?></span>
                <span class="product-availability <?php echo $hasStock ? 'in-stock' : 'out-of-stock'; ?>">
                    <?php echo $stockLabel; ?>
                </span>
            </div>

            <div class="product-buy-box">

                <form class="product-purchase" id="addToCartForm" method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?php echo intval($product['id']); ?>">
                    <div class="quantity-group">
                        <label for="quantity">Qty:</label>
                        <input
                            id="quantity"
                            type="number"
                            name="quantity"
                            min="1"
                            value="1"
                            <?php if ($product['stock'] !== null && $product['stock'] > 0): ?>
                                max="<?php echo intval($product['stock']); ?>"
                            <?php endif; ?>
                        >
                    </div>

                    <div class="purchase-buttons">
                        <button
                            type="submit"
                            class="btn btn-primary btn-full"
                            id="addToCartBtn"
                            <?php if (!$hasStock): ?>disabled<?php endif; ?>
                        >
                            Add to Cart
                        </button>
                        
                    </div>
                </form>

                <div class="product-shipping">
                    <p><strong>Free delivery</strong> within 5–7 business days.</p>
                    <p>Sold by <strong><?php echo htmlspecialchars($product['seller_name']); ?></strong> and fulfilled by Rising Tide.</p>
                </div>
            </div>
        </div>

    </div>

    <div class="product-description">
        <div class="product-description-header">
            <h2>Product description</h2>
        </div>

        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    </div>

</div>

<?php include('../includes/footer.php'); ?>

<div id="toast-container"></div>

<script src="../assets/js/script.js"></script>