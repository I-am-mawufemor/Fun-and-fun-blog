<?php
$page_title = "Forget Password | " . APP_NAME;
require_once __DIR__ . "/../../include/header.php";
require_once __DIR__ . "/../../include/navbar.php";

generateCSRF();

?>
<section class="login">
  <div class="login-container">
    <form action="?page=forgot-password" method="POST" class="login-form" id="forgotPasswordForm">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

      <h2 class="login-title">Forget Password</h2>
      <p class="login-subtitle">
        Enter your email to receive reset instructions
      </p>
      <div id="feedback" class="alert" aria-live="polite"></div>
      <div class="form-group">
        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="Enter your email"
          required>
      </div>

      <button type="submit" id="submitBtn" class="login-btn">
        Send Reset Instructions
      </button>

      <p class="forgot-password">
        <a href="?page=verify-reset-code">Already have reset code?</a>
      </p>
    </form>
  </div>
</section>
<script>
  const form = document.getElementById("forgotPasswordForm");
  const feedback = document.getElementById("feedback");
  const submitBtn = document.getElementById("submitBtn");

  form.addEventListener("submit", async function(e) {
    e.preventDefault();

    feedback.classList.remove("show", "alert-success", "alert-error");
    submitBtn.disabled = true;
    submitBtn.textContent = "Sending...";

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
      }
    } catch (err) {
      feedback.textContent = "Something went wrong. Please try again.";
      feedback.classList.add("show", "alert-error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Send Reset Instructions";
    }
  });
</script>

<?php
require_once __DIR__ . "/../../include/footer.php"; ?>