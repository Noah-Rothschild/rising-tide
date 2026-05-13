<?php
include('../includes/auth_check.php');
include('../includes/header.php');
include('../config/db.php');

$user_id = $_SESSION['user_id'];

/* =========================
   PRODUCT COUNT
========================= */

$productQuery = $conn->prepare("
    SELECT COUNT(*) AS total_products
    FROM products
    WHERE user_id = ?
");

$productQuery->bind_param("i", $user_id);

$productQuery->execute();

$productResult = $productQuery->get_result()->fetch_assoc();

$totalProducts = $productResult['total_products'];

/* =========================
   GET PRODUCTS
========================= */

$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $user_id);

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

            <div class="dashboard-grid">

                <?php while($row = $result->fetch_assoc()): ?>

                    <div class="dashboard-card">

                        <img
                            class="product-image"
                            src="../assets/images/uploads/<?php echo htmlspecialchars($row['image']); ?>"
                            alt=""
                        >

                        <div class="dashboard-card-content">

                            <h2>
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h2>

                            <p class="product-price">

                                R<?php echo number_format($row['price'], 2); ?>

                            </p>

                            <div class="card-buttons">

                                <a
                                    class="btn btn-primary"
                                    href="../products/edit.php?id=<?php echo $row['id']; ?>"
                                >
                                    Edit
                                </a>

                                <a
                                    class="btn btn-danger"
                                    href="../products/delete.php?id=<?php echo $row['id']; ?>"
                                >
                                    Delete
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

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