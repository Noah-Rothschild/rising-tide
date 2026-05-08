<?php include('../includes/header.php'); ?>

<div class="auth-page">

    <div class="auth-card">

        <h1>Welcome Back</h1>
        <p>Login to continue using Rising Tide.</p>

        <form action="process_login.php" method="POST">

            <input 
                type="email" 
                name="email" 
                placeholder="Email Address" 
                required
            >

            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required
            >

            <button type="submit">
                Login
            </button>

        </form>

        <p class="mt-20">
            Don’t have an account?
            <a href="register.php">Register here</a>
        </p>

    </div>

</div>

<?php include('../includes/footer.php'); ?>