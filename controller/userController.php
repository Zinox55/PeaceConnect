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
    session_start(); 

    $sql = "SELECT * FROM sign_up WHERE email = :email";
    $db = config::getConnexion();
    
    try {
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);

        if ($query->rowCount() > 0) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            if ($user['password'] === $password) {
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                return "success";
            }
        }
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }

    return "wrong email or password";
}



    public function deleteuser($name) {
        $sql = "DELETE FROM sign_up WHERE name = :name";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':name', $name);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM sign_up WHERE email = :email";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            
            // Explicitly set fetch mode to associative array
            $userData = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                return $userData;
            }
            return null;
        } catch (Exception $e) {
            error_log('getUserByEmail Error: ' . $e->getMessage());
            die('Error: ' . $e->getMessage());
        }
    }
public function updatePassword($email, $newPassword) {
    $sql = "UPDATE sign_up 
            SET password = :password, verify_password = :verify_password 
            WHERE email = :email";

    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        return $query->execute([
            'email' => $email,
            'password' => $newPassword,
            'verify_password' => $newPassword
        ]);
    } catch (Exception $e) {
        return false;
    }
}



 
}
?>