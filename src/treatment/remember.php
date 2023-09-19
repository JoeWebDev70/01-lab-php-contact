<?php
    require('connection_db.php');

    //declaration of variables 
    $data = "";
    $rememberToken = "";
    $rememberTime = "";
    $token = null;
    $time = null;
    $lifeTimeToken = 30*24*60*60; //1 month

    //get data from js localstorage 
    $data = json_decode(file_get_contents('php://input'));

    // if data are not null then process them
    if($data != null){

        $rememberToken = $data->token;
        $rememberTime = $data->time;
        // if rememberme token is set 
        if($rememberToken != "" && $rememberTime != ""){

            // connection to session
            session_start(); 

            // connection db
            $connection = connectionDb();

            //check in db if token was set for an user
            $sql = "SELECT iduser, user_name, user_email, user_pw, remember_date FROM user WHERE remember_token = :token";
            $sth = $connection->prepare($sql);
            $sth->bindParam(':token', $rememberToken, PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);

            //user found and datetimehash verify
            if($result > 0 && password_verify($result["remember_date"], $rememberTime)){ 
                
                if(time()- intval($result["remember_date"]) < $lifeTimeToken){// check if token is already available
                    $user =  ["id" => $result["iduser"], "name" => $result["user_name"]] ;
                    $_SESSION["user"] = $user; 
                    echo json_encode("access authorized");
                    
                }else{ //token life is passed delete values in db
                    $sql = "UPDATE user SET remember_token = :token, remember_date = :tokentime WHERE iduser=:id"; 
                    $sth = $connection->prepare($sql);
                    $sth->bindParam(':token', $token, PDO::PARAM_STR);
                    $sth->bindParam(':tokentime', $time, PDO::PARAM_STR);
                    $sth->bindParam(':id', $result["iduser"], PDO::PARAM_INT);
                    if(!$sth->execute()){//some error with update
                        echo json_encode("error on delete values of remember token in db");
                    }
                    $_SESSION["message"]["error"] = "Accès refusé ! Merci de vous reconnecter !";
                    echo json_encode("access denied");
                }
            }else{ //user not found or timehash is not correct
                
                if($result > 0){ //timehash is not correct then delete data in db
                    $sql = "UPDATE user SET remember_token = :token, remember_date = :tokentime WHERE iduser=:id"; 
                    $sth = $connection->prepare($sql);
                    $sth->bindParam(':token', $token, PDO::PARAM_STR);
                    $sth->bindParam(':tokentime', $time, PDO::PARAM_STR);
                    $sth->bindParam(':id', $result["iduser"], PDO::PARAM_INT);
                    if(!$sth->execute()){//some error with update
                        echo json_encode("error on delete values of remember token in db");
                    }
                }
                $_SESSION["message"]["error"] = "Accès refusé ! Merci de vous reconnecter ";
                echo json_encode("access denied");
            }
        }else{ //rememberme token was not send correctly
            $_SESSION["message"]["error"] = "Accès refusé ! Merci de vous reconnecter ";
            echo json_encode("access failed");
        }      
    }else{ //rememberme token was not send correctly
        $_SESSION["message"]["error"] = "Accès refusé ! Merci de vous reconnecter ";
        echo json_encode("access failed");
    } 
    

?>