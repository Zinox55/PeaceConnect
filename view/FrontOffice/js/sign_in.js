document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("sign");
    
    // CRITICAL: Exit if form doesn't exist
    if (!form) {
        return; // Stop here - don't add event listener
    }

    // Only reaches here if form exists
    form.addEventListener("submit", function (event) {

        let isValid = true;

        const user_email = document.getElementById("email");
        const user_password = document.getElementById("password");

        if (!user_email || !user_password) {
            return;
        }

        function displayMessage(id, message, isError) {
            const element = document.getElementById(id + "_error");
            if (element) {
                element.style.color = isError ? "red" : "green";
                element.innerText = message;
            }
        }

        displayMessage("email", "", false);
        displayMessage("password", "", false);

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!user_email.value.trim()) {
            displayMessage("email", "Email is required", true);
            isValid = false;
        } else if (!emailRegex.test(user_email.value.trim())) {
            displayMessage("email", "Invalid email format", true);
            isValid = false;
        }

        if (user_password.value.trim() === "") {
            displayMessage("password", "Password is required", true);
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }

    });

});