<?php

class Don {
    private $id_don;
    private $montant;
    private $devise;
    private $date_don;
    private $donateur_nom;
    private $message;
    private $methode_paiement;
    private $transaction_id;
    private $donateur_email;
    private $cause;

    // Constructor - Make sure parameter order matches!
    public function __construct($id_don, $montant, $devise, $date_don, $donateur_nom, $message, $methode_paiement, $transaction_id, $donateur_email, $id_cause ) {
        $this->id_don = $id_don;
        $this->montant = $montant;
        $this->devise = $devise;
        $this->date_don = $date_don;
        $this->donateur_nom = $donateur_nom;
        $this->message = $message;
        $this->methode_paiement = $methode_paiement;
        $this->transaction_id = $transaction_id;
        $this->donateur_email = $donateur_email;
        $this->cause = $id_cause;
    }

    // Getters
    public function getIdDon() { return $this->id_don; }
    public function getMontant() { return $this->montant; }
    public function getDevise() { return $this->devise; }
    public function getDateDon() { return $this->date_don; }
    public function getDonateurNom() { return $this->donateur_nom; }
    public function getMessage() { return $this->message; }
    public function getMethodePaiement() { return $this->methode_paiement; }
    public function getTransactionId() { return $this->transaction_id; }
    public function getDonateurEmail() { return $this->donateur_email; }
    public function getCause() { return $this->cause; }

    // Setters
    public function setIdDon($id_don) { $this->id_don = $id_don; }
    public function setMontant($montant) { $this->montant = $montant; }
    public function setDevise($devise) { $this->devise = $devise; }
    public function setDateDon($date_don) { $this->date_don = $date_don; }
    public function setDonateurNom($donateur_nom) { $this->donateur_nom = $donateur_nom; }
    public function setMessage($message) { $this->message = $message; }
    public function setMethodePaiement($methode_paiement) { $this->methode_paiement = $methode_paiement; }
    public function setTransactionId($transaction_id) { $this->transaction_id = $transaction_id; }
    public function setDonateurEmail($donateur_email) { $this->donateur_email = $donateur_email; }
    public function setCause($cause) { $this->cause = $cause; }
}

?>