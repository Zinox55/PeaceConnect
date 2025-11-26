<?php

class Cause {
    private $id_cause;
    private $nom_cause;
    private $description;

    // Constructor
    public function __construct($id_cause, $nom_cause, $description) {
        $this->id_cause = $id_cause;
        $this->nom_cause = $nom_cause;
        $this->description = $description;
    }

    // Getters
    public function getIdCause() { 
        return $this->id_cause; 
    }
    
    public function getNomCause() { 
        return $this->nom_cause; 
    }
    
    public function getDescription() { 
        return $this->description; 
    }

    // Setters
    public function setIdCause($id_cause) { 
        $this->id_cause = $id_cause; 
    }
    
    public function setNomCause($nom_cause) { 
        $this->nom_cause = $nom_cause; 
    }
    
    public function setDescription($description) { 
        $this->description = $description; 
    }
}

?>