<?php
    require('connection_db.php');

    //get data from js localstorage 
    $data = json_decode(file_get_contents('php://input'));

    // if data are not null then process them
    if($data != null){
        $rememberMe = $data->remember;
        // if rememberme token is set 
        if($rememberMe != ""){

            // connection to session
            session_start(); 

            // connection db
            $connection = connectionDb();
            if(!$connection){echo json_encode($connection);}
            
            //check in db if token was set for an user
            $sql = "SELECT iduser, user_name, user_email, user_pw FROM user WHERE user_remember = :remember";
            $sth = $connection->prepare($sql);
            $sth->bindParam(':remember', $rememberMe, PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);

            if($result > 0){ //user found
                $user =  ["id" => $result["iduser"], "name" => $result["user_name"]] ;
                $_SESSION["user"] = $user; 
                echo json_encode("access authorized");
            }else{ //user not found
                echo json_encode("access denied");
            }
        }else{ //rememberme token was not send correctly
            echo json_encode("access failed");
        }      
    }
    
?>