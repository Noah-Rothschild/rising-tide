<?php include('../includes/header.php'); ?>

<div class="auth-page">

    <div class="auth-left">

        <div class="auth-card">

            <div class="auth-title-box">
                <h1>CREATE ACCOUNT</h1>
            </div>

            <p class="auth-subtitle">
                Join Rising Tide and start selling today.
            </p>

            <form class="auth-form" action="process_register.php" method="POST">

                <div>
                    <label>Full Name</label>
                    <input
                        type="text"
                        name="username"
                        placeholder="Enter your full name"
                        required
                    >
                </div>

                <div>
                    <label>Email</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <div>
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Create a password"
                        required
                    >
                </div>

                <button class="auth-btn-primary" type="submit">
                    Register
                </button>

            </form>

            <div class="auth-footer">
                Already have an account?
                <a href="login.php">
                    Login here
                </a>
            </div>

        </div>

    </div>

    <div class="auth-right">

        <img
            src="../assets/images/login-art.png"
            alt="Register Art"
        >

    </div>

</div>

<?php include('../includes/footer.php'); ?>