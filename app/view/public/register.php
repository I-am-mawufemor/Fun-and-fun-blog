<?php
$page_title = "Register | " . APP_NAME;
require_once __DIR__ . "/../../include/header.php";
require_once __DIR__ . "/../../include/navbar.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.min.css">

<section class="login">
    <div class="login-container">

        <form action="?page=register" method="POST" class="login-form" id="registerForm">
             <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <h2 class="login-title">Register</h2>
            <p class="login-subtitle">
                Create an account to get started
            </p>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    placeholder="Enter your full name"
                    required>
            </div>

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
                <label for="telephone">Telephone Number</label>

                <input
                    type="tel"
                    id="telephone"
                    name="telephone"
                    placeholder="Enter your phone number"
                    required>

                <input
                    type="hidden"
                    id="full_phone"
                    name="full_phone">

                <span id="phone-message"></span>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="" disabled selected>Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
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

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <button type="submit" name="save" class="login-btn">
                Register
            </button>

            <p class="forgot-password">
                <a href="?page=login">Already have an account? Sign in</a>
            </p>

        </form>

    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>


<?php require_once __DIR__ . "/../../include/footer.php"; ?>