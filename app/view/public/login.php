<?php
$page_title = "Login | " . APP_NAME;
require_once __DIR__ . "/../../include/header.php";
require_once __DIR__ . "/../../include/navbar.php";

generateCSRF();

?>


<section class="login">
    <div class="login-container">

        <form action="?page=login" method="POST" class="login-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <h2 class="login-title">Member Login</h2>
            <p class="login-subtitle">
                Enter your email and password to log in
            </p>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    required>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <button type="submit" name="save" class="login-btn">
                Log In
            </button>

            <p class="forgot-password">
                <a href="?page=password-recovery">Forgot Password?</a>
            </p>

        </form>

    </div>
</section>


<?php
require_once __DIR__ . "/../../include/footer.php"; ?>