<?php
class Don {
    private ?int $id_don;
    private ?float $montant;
    private ?string $devise;
    private ?DateTime $date_don;
    private ?string $donateur_nom;
    private ?string $message;
    private ?string $methode_paiement;
    private ?string $transaction_id;
    private ?string $donateur_email;

    // Constructor
    public function __construct(
        ?int $id_don,
        ?float $montant,
        ?string $devise,
        ?DateTime $date_don,
        ?string $donateur_nom,
        ?string $message,
        ?string $methode_paiement,
        ?string $transaction_id,
        ?string $donateur_email
    ) {
        $this->id_don = $id_don;
        $this->montant = $montant;
        $this->devise = $devise;
        $this->date_don = $date_don;
        $this->donateur_nom = $donateur_nom;
        $this->message = $message;
        $this->methode_paiement = $methode_paiement;
        $this->transaction_id = $transaction_id;
        $this->donateur_email = $donateur_email;
    }

    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>ID</th>
                <th>Montant</th>
                <th>Devise</th>
                <th>Date du Don</th>
                <th>Donateur</th>
                <th>Message</th>
                <th>Méthode Paiement</th>
                <th>ID Transaction</th>
                <th>Email Donateur</th>
              </tr>";

        echo "<tr>";
        echo "<td>{$this->id_don}</td>";
        echo "<td>{$this->montant}</td>";
        echo "<td>{$this->devise}</td>";
        echo "<td>" . ($this->date_don ? $this->date_don->format('Y-m-d H:i:s') : '') . "</td>";
        echo "<td>{$this->donateur_nom}</td>";
        echo "<td>{$this->message}</td>";
        echo "<td>{$this->methode_paiement}</td>";
        echo "<td>{$this->transaction_id}</td>";
        echo "<td>{$this->donateur_email}</td>";
        echo "</tr>";

        echo "</table>";
    }

    // Getters and Setters
    public function getIdDon(): ?int {
        return $this->id_don;
    }

    public function setIdDon(?int $id_don): void {
        $this->id_don = $id_don;
    }

    public function getMontant(): ?float {
        return $this->montant;
    }

    public function setMontant(?float $montant): void {
        $this->montant = $montant;
    }

    public function getDevise(): ?string {
        return $this->devise;
    }

    public function setDevise(?string $devise): void {
        $this->devise = $devise;
    }

    public function getDateDon(): ?DateTime {
        return $this->date_don;
    }

    public function setDateDon(?DateTime $date_don): void {
        $this->date_don = $date_don;
    }

    public function getDonateurNom(): ?string {
        return $this->donateur_nom;
    }

    public function setDonateurNom(?string $donateur_nom): void {
        $this->donateur_nom = $donateur_nom;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(?string $message): void {
        $this->message = $message;
    }

    public function getMethodePaiement(): ?string {
        return $this->methode_paiement;
    }

    public function setMethodePaiement(?string $methode_paiement): void {
        $this->methode_paiement = $methode_paiement;
    }

    public function getTransactionId(): ?string {
        return $this->transaction_id;
    }

    public function setTransactionId(?string $transaction_id): void {
        $this->transaction_id = $transaction_id;
    }

    public function getDonateurEmail(): ?string {
        return $this->donateur_email;
    }

    public function setDonateurEmail(?string $donateur_email): void {
        $this->donateur_email = $donateur_email;
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
