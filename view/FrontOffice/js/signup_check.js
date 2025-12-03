document.addEventListener("DOMContentLoaded", function () {
const passwordInput = document.getElementById("password");
const strengthBar = document.getElementById("password_strength_bar");
const strengthText = document.getElementById("password_strength_text");

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

    const form = document.getElementById("signup_form");


    form.addEventListener("submit", function (event) {
        event.preventDefault(); 

        let isValid = true;

        const username = document.getElementById("name");
        const user_email = document.getElementById("email");
        const user_password = document.getElementById("password");          
        const user_password_v = document.getElementById("verify_password"); 

        function displayMessage(id, message, isError) {
            const el = document.getElementById(id + "_error");
            if (!el) {
                console.warn("Missing error element:", id + "_error", "message:", message);
                return;
            }
            el.style.color = isError ? "red" : "green";
            el.innerText = message;
        }

        
        ["name","email","password","verify_password"].forEach(id => displayMessage(id, "", false));

        
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

        const pw = (user_password && user_password.value) ? user_password.value : "";
        const pwConfirm = (user_password_v && user_password_v.value) ? user_password_v.value : "";

   
        if (pw.trim() === "") {
            displayMessage("password", "Password cannot be empty", true);
            isValid = false;
        } else if (!passwordRegex.test(pw)) {
            displayMessage("password", "Password must be 8+ chars and include upper, lower, number and special", true);
            isValid = false;
        }

        if (pw !== pwConfirm) {
            displayMessage("verify_password", "Passwords do not match", true);
            isValid = false;
        }

        if (isValid) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
            form.submit();
        }
    });

});
