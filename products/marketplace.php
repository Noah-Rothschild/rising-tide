<?php
include('../includes/header.php');
include('../config/db.php');

$query = "
    SELECT 
        products.*,
        users.username
    FROM products
    JOIN users
        ON products.user_id = users.id
    ORDER BY products.created_at DESC
";

$result = $conn->query($query);
?>

<div class="marketplace-container">

    <div class="marketplace-header">

        <h1>Marketplace</h1>

        <p>
            Browse products from the Rising Tide community.
        </p>

    </div>

    <div class="marketplace-grid">

        <?php while($product = $result->fetch_assoc()): ?>

            <div class="marketplace-card">

                <img 
                    class="marketplace-image"
                    src="../assets/images/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['title']); ?>"
                >

                <div class="marketplace-content">

                    <h2>
                        <?php echo htmlspecialchars($product['title']); ?>
                    </h2>

                    <p class="marketplace-price">
                        R <?php echo number_format($product['price'], 2); ?>
                    </p>

                    <p class="marketplace-seller">
                        Seller:
                        <?php echo htmlspecialchars($product['username']); ?>
                    </p>

                    <a 
                        class="btn btn-primary"
                        href="view.php?id=<?php echo $product['id']; ?>"
                    >
                        View Product
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</div>

<?php include('../includes/footer.php'); ?>