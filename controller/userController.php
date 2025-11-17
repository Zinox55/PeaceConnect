<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../model/sign_up.php');  // FIXED: Added / before model

class userController {
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
}
?>