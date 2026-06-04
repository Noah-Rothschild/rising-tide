<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta 
        name="viewport" 
        content="width=device-width, initial-scale=1.0"
    >

    <title>Rising Tide</title>

    <link rel="stylesheet" href="/rising-tide/assets/css/global.css">

    <link rel="stylesheet" href="/rising-tide/assets/css/navbar.css">

    <link rel="stylesheet" href="/rising-tide/assets/css/dashboard.css">

    <link rel="stylesheet" href="/rising-tide/assets/css/forms.css">

    <link rel="stylesheet" href="/rising-tide/assets/css/auth.css">

    <link rel="stylesheet" href="/rising-tide/assets/css/marketplace.css">

    <link rel="stylesheet" href="/rising-tide/assets/css/product.css">

    <link rel="stylesheet" href="/rising-tide/assets/css/index.css">

    <link rel="stylesheet" href="/rising-tide/assets/css/admin.css">

</head>

<body>

<nav class="navbar">


    <div class="navbar-left">

        <a href="/../" class="logo">
            Rising Tide
        </a>

    </div>


    <div class="navbar-center">

        <a href="/../">
            Home
        </a>

        <a href="/rising-tide/products/marketplace.php">
            Marketplace
        </a>

        <a href="/../products/create.php">
            Sell
        </a>

    </div>


    <div class="navbar-right">

        <form class="navbar-search">

            <input 
                type="text"
                placeholder="Search products..."
            >

        </form>

        <a href="/../products/cart.php" class="cart-icon">
            <img src="/rising-tide/assets/images/cart.jpg" alt="Cart">
        </a>


        <?php if(isset($_SESSION['user_id'])): ?>

            <div class="dropdown">

                <button class="dropdown-btn">

                    <?php echo $_SESSION['username']; ?> ▼

                </button>

                <div class="dropdown-content">

                    <a href="/rising-tide/user/dashboard.php">
                        Dashboard
                    </a>

                    <a href="/rising-tide/user/dashboard.php#settings">
                        Account Settings
                    </a>

                    <a href="/rising-tide/auth/logout.php">
                        Sign Out
                    </a>

                </div>

            </div>

        <?php else: ?>

            <a class="login-link" href="/../auth/login.php">
                Login
            </a>

        <?php endif; ?>

    </div>

</nav>