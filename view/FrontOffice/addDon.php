<?php
require_once __DIR__ . '/../../Controller/DonController.php';
require_once __DIR__ . '/../../Model/Don.php';

$error = "";
$donC = new DonController();

// Check if all fields exist in POST
if (
    isset($_POST["montant"]) &&
    isset($_POST["nom"]) &&
    isset($_POST["email"]) &&
    isset($_POST["payment_method"]) &&
    isset($_POST["cause"])
) {

    // Validate that they are not empty
    if (
        !empty($_POST["montant"]) &&
        !empty($_POST["nom"]) &&
        !empty($_POST["email"]) &&
        !empty($_POST["payment_method"]) &&
        !empty($_POST["cause"])
    ) {

        // Create the Don object
        $don = new Don(
            null,                   // id_don (auto-increment)
            floatval($_POST["montant"]),
            new DateTime(),         // date_don = today's date
            $_POST["nom"],
            $_POST["email"],
            $_POST["payment_method"],
            intval($_POST["cause"]) // FK id_cause
        );

        // Add into DB
        $donC->addDon($don);

        // Redirect to list page
        header('Location: donList.php');
        exit;

    } else {
        $error = "Missing information";
    }
}
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize your addDon validation
    // For example, call your external JS function or include the code here

    // Example: If your adddon.js has a function called validerFormulaire
    const form = document.getElementById("addDonForm"); // make sure your form has this ID
    if (form) {
        form.addEventListener("submit", validerFormulaire);
    }

    // Optional: you can also put any inline validation here
    console.log("addDon.js initialized properly!");
});
</script>

?>
