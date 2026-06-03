<?php
include('../includes/auth_check.php');
include('../includes/header.php');
include('../config/db.php');

$id = $_SESSION['user_id'];


$productQuery = $conn->prepare("
    SELECT COUNT(*) AS total_products
    FROM products
    WHERE seller_id = ?
");

$productQuery->bind_param("i", $id);

$productQuery->execute();

$productResult = $productQuery->get_result()->fetch_assoc();

$totalProducts = $productResult['total_products'];


$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE seller_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();
?>

<div class="dashboard-layout">

    <!-- SIDEBAR -->

    <aside class="dashboard-sidebar">

        <h2>Seller Hub</h2>

        <button class="sidebar-btn active" data-tab="overview">
            Overview
        </button>

        <button class="sidebar-btn" data-tab="products">
            Products
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

        <!-- PRODUCTS -->

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

                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
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