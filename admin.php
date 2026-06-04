<?php
include('includes/auth_check.php');
include('includes/header.php');
include('config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("Access denied");
}

$tab = $_GET['tab'] ?? 'users';

/*
    DATA QUERIES
*/

// USERS
$usersResult = $conn->query("
    SELECT id, username, email, role, created_at 
    FROM users 
    ORDER BY id DESC
");

// PRODUCTS
$productsResult = $conn->query("
    SELECT p.*, u.username AS seller_name
    FROM products p
    LEFT JOIN users u ON p.seller_id = u.id
    ORDER BY p.created_at DESC
");

// ORDERS
$ordersResult = $conn->query("
    SELECT * 
    FROM orders 
    ORDER BY created_at DESC
");
?>

<div class="dashboard-layout">

    <!-- SIDEBAR -->
    <aside class="dashboard-sidebar">

        <h2>Admin Panel</h2>

        <a href="?tab=users" class="sidebar-btn <?= $tab === 'users' ? 'active' : '' ?>">
            Users
        </a>

        <a href="?tab=products" class="sidebar-btn <?= $tab === 'products' ? 'active' : '' ?>">
            Products
        </a>

        <a href="?tab=orders" class="sidebar-btn <?= $tab === 'orders' ? 'active' : '' ?>">
            Orders
        </a>

    </aside>

    <main class="dashboard-main">

    <!-- ================= USERS ================= -->
    <?php if ($tab === 'users'): ?>

        <section class="dashboard-tab">

            <h1>Users</h1>

            <div class="dashboard-table">

                <div class="dashboard-table-header">
                    <span>ID</span>
                    <span>Username</span>
                    <span>Email</span>
                    <span>Role</span>
                    <span>Actions</span>
                </div>

                <?php while ($u = $usersResult->fetch_assoc()): ?>
                <div class="dashboard-table-row">
                    <span><?= $u['id'] ?></span>
                    <span><?= htmlspecialchars($u['username']) ?></span>
                    <span><?= htmlspecialchars($u['email']) ?></span>
                    <span><?= htmlspecialchars($u['role']) ?></span>
                    <span>
                        <a class="btn btn-danger"
                           href="delete_user.php?id=<?= $u['id'] ?>"
                           onclick="return confirm('Delete this user?')">
                            Delete
                        </a>
                    </span>
                </div>
                <?php endwhile; ?>

            </div>

        </section>

    <?php endif; ?>


    <!-- ================= PRODUCTS ================= -->
    <?php if ($tab === 'products'): ?>

        <section class="dashboard-tab">

            <h1>Products</h1>

            <div class="dashboard-table">

                <div class="dashboard-table-header">
                    <span>Name</span>
                    <span>Seller</span>
                    <span>Stock</span>
                    <span>Price</span>
                    <span>Actions</span>
                </div>

                <?php while ($p = $productsResult->fetch_assoc()): ?>
                <div class="dashboard-table-row">
                    <span><?= htmlspecialchars($p['name']) ?></span>
                    <span><?= htmlspecialchars($p['seller_name'] ?? 'Unknown') ?></span>
                    <span><?= intval($p['stock']) ?></span>
                    <span>R<?= number_format($p['price'], 2) ?></span>
                    <span>
                        <a class="btn btn-danger"
                           href="delete_product.php?id=<?= $p['id'] ?>"
                           onclick="return confirm('Delete this product?')">
                            Delete
                        </a>
                    </span>
                </div>
                <?php endwhile; ?>

            </div>

        </section>

    <?php endif; ?>


    <!-- ================= ORDERS ================= -->
    <?php if ($tab === 'orders'): ?>

        <section class="dashboard-tab">

            <h1>Orders</h1>

            <div class="dashboard-table">

                <div class="dashboard-table-header">
                    <span>Order ID</span>
                    <span>Date</span>
                    <span>Total</span>
                    <span>Status</span>
                    <span>Payment</span>
                </div>

                <?php while ($o = $ordersResult->fetch_assoc()): ?>
                <div class="dashboard-table-row">
                    <span>#<?= $o['id'] ?></span>
                    <span><?= htmlspecialchars($o['created_at']) ?></span>
                    <span>R<?= number_format($o['total_amount'], 2) ?></span>
                    <span><?= htmlspecialchars($o['status']) ?></span>
                    <span><?= htmlspecialchars($o['payment_status']) ?></span>
                </div>
                <?php endwhile; ?>

            </div>

        </section>

    <?php endif; ?>


    </main>

</div>

<?php include('includes/footer.php'); ?>