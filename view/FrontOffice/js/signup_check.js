document.addEventListener("DOMContentLoaded", function () {
    
    const passwordInput = document.getElementById("password");
    const strengthBar = document.getElementById("password_strength_bar");
    const strengthText = document.getElementById("password_strength_text");

    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener("input", function () {
            const pw = passwordInput.value;

            let score = 0;

            if (pw.length >= 8) score++;       
            if (/[a-z]/.test(pw)) score++;     
            if (/[A-Z]/.test(pw)) score++;      
            if (/\d/.test(pw)) score++;        
            if (/[\W_]/.test(pw)) score++;     

            const percent = (score / 5) * 100;
            strengthBar.style.width = percent + "%";

            if (score <= 1) {
                strengthBar.style.background = "red";
                strengthText.innerText = "Weak password";
                strengthText.style.color = "red";
            } else if (score === 2 || score === 3) {
                strengthBar.style.background = "orange";
                strengthText.innerText = "Medium strength";
                strengthText.style.color = "orange";
            } else if (score === 4) {
                strengthBar.style.background = "blue";
                strengthText.innerText = "Strong password";
                strengthText.style.color = "blue";
            } else {
                strengthBar.style.background = "green";
                strengthText.innerText = "Very strong password";
                strengthText.style.color = "green";
            }
        });
    }
    const form = document.getElementById("signup_form");

    if (!form) {
        return; // Exit if form doesn't exist
    }

    form.addEventListener("submit", function (event) {

        let isValid = true;

        const username = document.getElementById("name");
        const user_email = document.getElementById("email");
        const user_password = document.getElementById("password");
        const user_password_v = document.getElementById("verify_password");

        function displayMessage(id, message, isError) {
            const element = document.getElementById(id + "_error");
            if (element) {
                element.style.color = isError ? "red" : "green";
                element.innerText = message;
            }
        }

        displayMessage("name", "", false);
        displayMessage("email", "", false);
        displayMessage("password", "", false);
        displayMessage("verify_password", "", false);

        if (!username || username.value.trim() === "") {
            displayMessage("name", "You have to type a username", true);
            isValid = false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!user_email || !emailRegex.test(user_email.value.trim())) {
            displayMessage("email", "Invalid email", true);
            isValid = false;
        }

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!user_password || user_password.value === "") {
            displayMessage("password", "Password cannot be empty", true);
            isValid = false;
        } else if (!passwordRegex.test(user_password.value)) {
            displayMessage("password", "Password must be 8+ chars with uppercase, lowercase, number and special character", true);
            isValid = false;
        }

        if (!user_password_v || user_password.value !== user_password_v.value) {
            displayMessage("verify_password", "Passwords do not match", true);
            isValid = false;
        }


        if (!isValid) {
            event.preventDefault();
            console.log("Validation failed - form NOT submitted");
        } else {
            console.log("Validation passed - form submitting to PHP");
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
        }

    });

});