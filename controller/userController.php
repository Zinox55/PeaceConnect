<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../model/sign_up.php'); 

class userController {
    public function listusers() {
        $sql = "SELECT * FROM sign_up";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function adduser(Sign_up $user) {
        $sql = "INSERT INTO sign_up (name, email, password, verify_password) VALUES (:name, :email, :password, :verify_password)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'name' => $user->getName(),
                'email' => $user->getemail(),
                'password' => $user->getpassword(),
                'verify_password' => $user->getverify_password(),
            ]);
            
            if($result) {
                echo "Success! Data inserted.";
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
public function connectUser($email, $password) {
    $sql = "SELECT email, password FROM sign_up WHERE email = :email";
    $db = config::getConnexion();
    $message = "wrong email or password";
    
    try {
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        
        if ($query->rowCount() > 0) {
            $user = $query->fetch();
            // If using password hashing (recommended):
            // if (password_verify($password, $user['password'])) {
            
            // Current plain text comparison (not recommended):
            if ($user['password'] === $password) {
                $message = "success";
            }
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
    
    return $message;
}
}
?>