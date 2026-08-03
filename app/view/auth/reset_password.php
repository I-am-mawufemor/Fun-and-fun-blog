<?php
$page_title = "Reset Password | " . APP_NAME;
require_once __DIR__ . "/../../include/header.php";
require_once __DIR__ . "/../../include/navbar.php";

generateCSRF();

?>
<section class="login">
    <div class="login-container">
        <form action="?page=resetPassword" method="POST" class="login-form" id="resetPasswordForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <h2 class="login-title">Reset Password</h2>
            <p class="login-subtitle">
                Enter a new password for your account
            </p>
            <div id="feedback" class="alert" aria-live="polite"></div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter new password"
                    minlength="8"
                    required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm new password"
                    minlength="8"
                    required>
            </div>

            <button type="submit" id="submitBtn" class="login-btn">
                Reset Password
            </button>

        </form>
    </div>
</section>
<script>
    const form = document.getElementById("resetPasswordForm");
    const feedback = document.getElementById("feedback");
    const submitBtn = document.getElementById("submitBtn");

    form.addEventListener("submit", async function(e) {
        e.preventDefault();

        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirm_password").value;

        feedback.classList.remove("show", "alert-success", "alert-error");

        if (password !== confirmPassword) {
            feedback.textContent = "Passwords do not match.";
            feedback.classList.add("show", "alert-error");
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = "Resetting...";

        try {
            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
            });

            const result = await response.json();

            feedback.textContent = result.message;
            feedback.classList.add(
                "show",
                result.success ? "alert-success" : "alert-error",
            );

            if (result.success) {
                form.reset();
                submitBtn.textContent = "Redirecting...";
                setTimeout(() => {
                    window.location.href = "?page=login";
                }, 1500);
                return;
            }
        } catch (err) {
            feedback.textContent = "Something went wrong. Please try again.";
            feedback.classList.add("show", "alert-error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = "Reset Password";
        }
    });
</script>

<?php
require_once __DIR__ . "/../../include/footer.php"; ?>