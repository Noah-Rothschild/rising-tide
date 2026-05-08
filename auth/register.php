<?php include('../includes/header.php'); ?>

<div class="auth-page">

    <div class="auth-card">

        <h1>Create Account</h1>
        <p>Join Rising Tide and start selling today.</p>

        <form action="process_register.php" method="POST">

            <input 
                type="text" 
                name="username" 
                placeholder="Username" 
                required
            >

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
                Register
            </button>

        </form>

        <p class="mt-20">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </div>

</div>

<?php include('../includes/footer.php'); ?>