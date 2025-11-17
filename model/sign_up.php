<?php
class Sign_up {
    private $name;
    private $email;
    private $password;
    private $verify_password;
       public function __construct($name, $email, $password, $verify_password) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->verify_password = $verify_password;
    }
    
    // Getters and Setters
    public function getName(){
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
        public function getemail() {
        return $this->email;
    }

    public function setemail($email) {
        $this->email = $email;
    }
        public function getpassword(){
        return $this->password;
    }

    public function setpassword($password)  {
        $this->password = $password;
    }
    public function getverify_password(){
        return $this->verify_password;
    }

    public function setverify_password($verify_password){
        $this->verify_password = $verify_password;
    }
    

}
?>