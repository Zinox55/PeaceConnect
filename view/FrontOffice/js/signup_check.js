document.addEventListener("DOMContentLoaded", function () {

    form = document.getElementById("signup_form");

    form.addEventListener("submit", function (event) {

        let isValid = true;

        const username = document.getElementById("name");
        const user_email = document.getElementById("email");
        const user_password = document.getElementById("password");
        const user_password_v = document.getElementById("verify_password");

        function displayMessage(id, message, isError) {
            const element = document.getElementById(id + "_error");
            element.style.color = isError ? "red" : "green";
            element.innerText = message;
        }

        displayMessage("name", "", false);
        displayMessage("email", "", false);
        displayMessage("password", "", false);
        displayMessage("verify_password", "", false);

        if (username.value.trim() === "") {
            displayMessage("name", "You have to type a username", true);
            isValid = false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(user_email.value.trim())) {
            displayMessage("email", "Invalid email", true);
            isValid = false;
        }

        if (user_password.value === "") {
            displayMessage("password", "Password cannot be empty", true);
            isValid = false;
        }

        if (user_password.value !== user_password_v.value) {
            displayMessage("verify_password", "Passwords do not match", true);
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }

    });

});
