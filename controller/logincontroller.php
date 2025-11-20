<?php  
start_session();





class loginController(){

public function login($email, $password) {
    $sql = "SELECT * FROM sign_up WHERE email = :email ";
    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute();

        if (!$user) {
            return "Email not found!";
        }

        if ($password === $user['password']) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];

            return "success";
        } else {
            return "Incorrect password!";
        }

    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}



}




?>