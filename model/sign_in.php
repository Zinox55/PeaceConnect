<?php
enum Role: string {
    case admin = 'admin';
    case user = 'user';
    case journalist = 'journalist';
}
class Sign_in {
    private ?string $name;
    private ?string $email;
    private ?string $password;
    private ?string $verify_password;

    private ?Role $role;
       public function __construct(?string $name, ?string $email, ?string $password, ?string $verify_password, ?Role $role) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->verify_password = $verify_password;
        $this->role = $role;
    }
    
    // Getters and Setters
    public function getName(): ?string {
        return $this->name;
    }

    public function setId(?string $name): void {
        $this->name = $name;
    }
        public function getemail(): ?string {
        return $this->email;
    }

    public function setemail(?string $email): void {
        $this->name = $email;
    }
        public function getpassword(): ?string {
        return $this->password;
    }

    public function setpassword(?string $password): void {
        $this->password = $password;
    }
    public function getverify_password(): ?string {
        return $this->verify_password;
    }

    public function setverify_password(?string $verify_password): void {
        $this->password = $verify_password;
    }
        public function getrole(): ?Role {
        return $this->role;
    }

    public function setrole(?role $role): void {
        $this->password = $role;
    }


}
?>