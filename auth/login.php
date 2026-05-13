<?php include('../includes/header.php'); ?>

<div class="auth-page">

    <!-- LEFT SIDE -->

    <div class="auth-left">

        <div class="auth-card">

            <div class="auth-title-box">
                <h1>WELCOME BACK</h1>
            </div>

            <p class="auth-subtitle">
                Please enter your details.
            </p>

            <form class="auth-form" action="process_login.php" method="POST">

                <!-- EMAIL -->

                <div>
                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <!-- PASSWORD -->

                <div>
                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="********"
                        required
                    >
                </div>

                <!-- OPTIONS -->

                <div class="auth-options">

                    <label class="remember-me">
                        <input type="checkbox">
                        Remember me
                    </label>

                    <a href="#">
                        Forgot password
                    </a>

                </div>

                <!-- BUTTONS -->

                <button class="auth-btn-primary" type="submit">
                    Sign in
                </button>

                <button class="auth-btn-google" type="button">
                    Sign in with Google
                </button>

            </form>

            <div class="auth-footer">

                Don’t have an account?

                <a href="register.php">
                    Sign up for free!
                </a>

            </div>

        </div>

    </div>

    <!-- RIGHT SIDE IMAGE -->

    <div class="auth-right">

        <img
            src="../assets/images/login-art.png"
            alt="Login Art"
        >

    </div>

</div>

<?php include('../includes/footer.php'); ?>