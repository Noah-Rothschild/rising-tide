<?php
include('../includes/auth_check.php');
include('../includes/header.php');
include('../config/db.php');

$id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'buyer';


$totalProducts = 0;
$productsResult = null;
if ($role === 'seller') {
    $productQuery = $conn->prepare("SELECT COUNT(*) AS total_products FROM products WHERE seller_id = ?");
    $productQuery->bind_param("i", $id);
    $productQuery->execute();
    $productResult = $productQuery->get_result()->fetch_assoc();
    $totalProducts = $productResult['total_products'];

    $stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $productsResult = $stmt->get_result();
}

$orders = [];
if ($role === 'buyer') {
    $ordersStmt = $conn->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY created_at DESC");
    $ordersStmt->bind_param("i", $id);
    $ordersStmt->execute();
    $orders = $ordersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $ordersStmt = $conn->prepare(
        "SELECT o.* FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE oi.seller_id = ? GROUP BY o.id ORDER BY o.created_at DESC"
    );
    $ordersStmt->bind_param("i", $id);
    $ordersStmt->execute();
    $orders = $ordersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="dashboard-layout">

    <!-- SIDEBAR -->

    <aside class="dashboard-sidebar">

        <h2>Seller Hub</h2>

        <button class="sidebar-btn active" data-tab="overview">
            Overview
        </button>

        <?php if ($role === 'seller'): ?>
        <button class="sidebar-btn" data-tab="products">
            Products
        </button>
        <?php endif; ?>

        <button class="sidebar-btn" data-tab="orders">
            Orders
        </button>

        <button class="sidebar-btn" data-tab="analytics">
            Analytics
        </button>

        <button class="sidebar-btn" data-tab="settings">
            Settings
        </button>

    </aside>

    <!-- MAIN CONTENT -->

    <main class="dashboard-main">

        <!-- OVERVIEW -->

        <section class="dashboard-tab active-tab" id="overview">

            <h1>
                Welcome back,
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </h1>

            <div class="stats-grid">

                <div class="stat-card">

                    <h3>Total Products</h3>

                    <p><?php echo $totalProducts; ?></p>

                </div>

                <div class="stat-card">

                    <h3>Total Sales</h3>

                    <p>14</p>

                </div>

                <div class="stat-card">

                    <h3>Revenue</h3>

                    <p>R8,420</p>

                </div>

            </div>

        </section>

        <!-- PRODUCTS (sellers only) -->
        <?php if ($role === 'seller'): ?>
        <section class="dashboard-tab" id="products">

            <div class="products-header">

                <h1>My Listings</h1>

                <a 
                    href="../products/create.php"
                    class="btn btn-primary"
                >
                    Add Product
                </a>

            </div>

            <div class="dashboard-table">

                <div class="dashboard-table-header">
                    <span>Product</span>
                    <span>Stock</span>
                    <span>Price</span>
                    <span>Actions</span>
                </div>

                <?php if ($productsResult && $productsResult->num_rows > 0): ?>
                    <?php while($row = $productsResult->fetch_assoc()): ?>
                        <div class="dashboard-table-row">
                            <span class="dashboard-table-cell product-name">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </span>
                            <span class="dashboard-table-cell stock">
                                <?php echo intval($row['stock']); ?>
                            </span>
                            <span class="dashboard-table-cell price">
                                R<?php echo number_format($row['price'], 2); ?>
                            </span>
                            <span class="dashboard-table-cell actions">
                                <a class="btn btn-secondary" href="../products/edit.php?id=<?php echo $row['id']; ?>">
                                    Edit
                                </a>
                                <a class="btn btn-danger" href="../products/delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?')">
                                    Delete
                                </a>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>You have no product listings.</p>
                <?php endif; ?>

            </div>

        </section>
        <?php endif; ?>

        <!-- ORDERS -->
        <section class="dashboard-tab" id="orders">

            <h1>Orders</h1>

            <?php if (empty($orders)): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <div class="dashboard-table">

                    <div class="dashboard-table-header">
                        <span>Order ID</span>
                        <span>Date</span>
                        <span>Total</span>
                        <span>Status</span>
                        <span>Payment Status</span>
                    </div>

                    <?php foreach ($orders as $order): ?>
                        <div class="dashboard-table-row">
                            <span class="dashboard-table-cell order-id">
                                #<?php echo intval($order['id']); ?>
                            </span>
                            <span class="dashboard-table-cell date">
                                <?php echo htmlspecialchars($order['created_at']); ?>
                            </span>
                            <span class="dashboard-table-cell total">
                                R<?php echo number_format($order['total_amount'], 2); ?>
                            </span>
                            <span class="dashboard-table-cell status">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                            <span class="dashboard-table-cell payment">
                                <?php echo htmlspecialchars($order['payment_status']); ?>
                            </span>
                        </div>

                        <!-- Order Items -->
                        <div class="order-items-subrow">
                            <div style="padding: 10px 15px; background: #f9f9f9; border-left: 3px solid #ddd;">
                                <strong>Items:</strong>
                                <ul style="margin: 8px 0 0 20px; padding: 0;">
                                <?php
                                if ($role === 'buyer') {
                                    $itemsStmt = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                                    $itemsStmt->bind_param("i", $order['id']);
                                } else {
                                    $itemsStmt = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ? AND oi.seller_id = ?");
                                    $itemsStmt->bind_param("ii", $order['id'], $id);
                                }
                                $itemsStmt->execute();
                                $itemsResult = $itemsStmt->get_result();
                                ?>
                                <?php while ($it = $itemsResult->fetch_assoc()): ?>
                                    <li><?php echo htmlspecialchars($it['name']); ?> — Qty: <?php echo intval($it['quantity']); ?> — R<?php echo number_format($it['price'], 2); ?></li>
                                <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

        </section>

        <!-- ANALYTICS -->

        <section class="dashboard-tab" id="analytics">

            <h1>Analytics</h1>

            <div class="analytics-placeholder">

                <div class="bar" style="height: 70%;"></div>
                <div class="bar" style="height: 40%;"></div>
                <div class="bar" style="height: 90%;"></div>
                <div class="bar" style="height: 60%;"></div>
                <div class="bar" style="height: 80%;"></div>

            </div>

        </section>

        <!-- SETTINGS -->

        <section class="dashboard-tab" id="settings">

            <h1>Account Settings</h1>

            <p>Profile management coming soon.</p>

        </section>

    </main>

</div>

<script>

const buttons = document.querySelectorAll('.sidebar-btn');

const tabs = document.querySelectorAll('.dashboard-tab');


function activateTab(tabName) {
    const button = document.querySelector(`.sidebar-btn[data-tab="${tabName}"]`);
    if (button) {
        buttons.forEach(btn => btn.classList.remove('active'));
        tabs.forEach(tab => tab.classList.remove('active-tab'));
        button.classList.add('active');
        document.getElementById(tabName).classList.add('active-tab');
    }
}

window.addEventListener('load', () => {
    const hash = window.location.hash.substring(1); // Remove #
    if (hash) {
        activateTab(hash);
    }
});

buttons.forEach(button => {

    button.addEventListener('click', () => {

        const target = button.dataset.tab;

        activateTab(target);

    });

});

</script>

<?php include('../includes/footer.php'); ?>