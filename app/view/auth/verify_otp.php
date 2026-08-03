<?php
$page_title = "Verify Reset Code | " . APP_NAME;
require_once __DIR__ . "/../../include/header.php";
require_once __DIR__ . "/../../include/navbar.php";

generateCSRF();

?>
<section class="login">
  <div class="login-container">
    <form action="?page=verify-reset-code" method="POST" class="login-form" id="verifyOtpForm">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

      <h2 class="login-title">Verify Reset Code</h2>
      <p class="login-subtitle">
        Enter the code sent to your email or phone
      </p>
      <div id="feedback" class="alert" aria-live="polite"></div>
      <div class="form-group">
        <label for="otpCode">Reset Code</label>
        <input
          type="text"
          inputmode="numeric"
          pattern="[0-9]{6}"
          maxlength="6"
          id="otpCode"
          name="otpCode"
          placeholder="Enter 6-digit code"
          required>
      </div>

      <button type="submit" id="submitBtn" class="login-btn">
        Verify Reset Code
      </button>

    </form>
  </div>
</section>
<script>
  const form = document.getElementById("verifyOtpForm");
  const feedback = document.getElementById("feedback");
  const submitBtn = document.getElementById("submitBtn");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    feedback.classList.remove("show", "alert-success", "alert-error");
    submitBtn.disabled = true;
    submitBtn.textContent = "Verifying...";

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
        submitBtn.textContent = "Redirecting...";
        window.location.href = "?page=resetPassword";
        return;
      }
    } catch (err) {
      feedback.textContent = "Something went wrong. Please try again.";
      feedback.classList.add("show", "alert-error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Verify Reset Code";
    }
  });
</script>

<?php
require_once __DIR__ . "/../../include/footer.php"; ?>