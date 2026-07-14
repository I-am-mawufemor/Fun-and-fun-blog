const form = document.getElementById("categoryForm");
const feedback = document.getElementById("feedback");
const submitBtn = document.getElementById("submitBtn");

form.addEventListener("submit", async function (e) {
  e.preventDefault();

  feedback.classList.remove("show", "alert-success", "alert-error");
  submitBtn.disabled = true;
  submitBtn.textContent = "Creating...";

  try {
    const formData = new FormData(form);

    const response = await fetch(form.action, {
      method: "POST",
      body: formData,
    });

    // const rawText = await response.text();
    // console.log("RAW RESPONSE:", rawText); // <-- add this
    // console.log("STATUS:", response.status); // <-- and this

    // const result = JSON.parse(rawText);

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
    submitBtn.textContent = "Create category";
  }
});
