document.addEventListener("DOMContentLoaded", () => {

    const input = document.querySelector("#telephone");
    const form = document.querySelector("#registerForm");
    const phoneMessage = document.querySelector("#phone-message");
    const hiddenPhone = document.querySelector("#full_phone");

    const iti = window.intlTelInput(input, {
        initialCountry: "gh",
        preferredCountries: ["gh", "ng", "tg", "bj", "us", "gb"],
        separateDialCode: true,
        strictMode: true,
        loadUtils: () =>
            import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js")
    });

    const errorMap = {
        0: "Invalid phone number",
        1: "Invalid country code",
        2: "Number is too short",
        3: "Number is too long",
        4: "Invalid phone number"
    };

    function resetValidation() {
        phoneMessage.textContent = "";
        phoneMessage.className = "";
        input.classList.remove("phone-valid", "phone-invalid");
    }

    function validatePhone() {

        resetValidation();

        if (!input.value.trim()) {
            return false;
        }

        if (iti.isValidNumber()) {

            input.classList.add("phone-valid");

            phoneMessage.textContent =
                "Valid phone number";

            phoneMessage.classList.add("phone-success");

            return true;

        } else {

            const errorCode =
                iti.getValidationError();

            input.classList.add("phone-invalid");

            phoneMessage.textContent =
                errorMap[errorCode] ||
                "Invalid phone number";

            phoneMessage.classList.add("phone-error");

            return false;
        }
    }

    input.addEventListener("blur", validatePhone);

    input.addEventListener("input", () => {
        resetValidation();
    });

    form.addEventListener("submit", (e) => {

        if (!input.value.trim()) {

            e.preventDefault();

            phoneMessage.textContent =
                "Phone number is required.";

            phoneMessage.classList.add("phone-error");

            input.classList.add("phone-invalid");

            return;
        }

        if (!validatePhone()) {
            e.preventDefault();
            return;
        }

        // Store international format
        hiddenPhone.value = iti.getNumber();

        console.log("International Number:",
            hiddenPhone.value);

        // Form can now submit normally
    });

});
