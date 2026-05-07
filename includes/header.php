<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rising Tide</title>

    <link rel="stylesheet" href="/rising-tide/assets/css/style.css">
</head>
<body>

<nav style="background:#222; padding:15px;">
    <a href="/rising-tide/" style="color:white; margin-right:15px;">Home</a>

    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="/rising-tide/user/dashboard.php" style="color:white; margin-right:15px;">Dashboard</a>
        <a href="/rising-tide/auth/logout.php" style="color:white;">Logout</a>
    <?php else: ?>
        <a href="/rising-tide/auth/login.php" style="color:white; margin-right:15px;">Login</a>
        <a href="/rising-tide/auth/register.php" style="color:white;">Register</a>
    <?php endif; ?>
</nav>