<?php
include('../includes/auth_check.php');
include('../includes/header.php');
?>

<div class="dashboard-container">

    <div class="dashboard-header">
        <h1>Welcome, <?php echo $_SESSION['username']; ?> 👋</h1>
        <p>Manage your marketplace activity from your dashboard.</p>
    </div>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h2>My Listings</h2>
            <p>View and manage your products.</p>
            <a href="../products/list.php" class="btn">View Listings</a>
        </div>

        <div class="dashboard-card">
            <h2>Sell Item</h2>
            <p>Create a new product listing.</p>
            <a href="../products/create.php" class="btn">Create Listing</a>
        </div>

        <div class="dashboard-card">
            <h2>Transactions</h2>
            <p>Track purchases and sales.</p>
            <a href="../transactions/history.php" class="btn">View Transactions</a>
        </div>

        <div class="dashboard-card">
            <h2>Reviews</h2>
            <p>Check your ratings and feedback.</p>
            <a href="../reviews/" class="btn">View Reviews</a>
        </div>

        <?php if ($_SESSION['role'] == 'admin'): ?>
        <div class="dashboard-card admin-card">
            <h2>Admin Panel</h2>
            <p>Manage users and reports.</p>
            <a href="../admin/dashboard.php" class="btn">Open Admin</a>
        </div>
        <?php endif; ?>

    </div>

</div>

<?php include('../includes/footer.php'); ?>