<?php
   require('check_functions.php');
   require('connection_db.php');
   
   //connection to session
   session_start(); 

  //connection db
   $connection = connectionDb();
   if(!$connection){
       header('location: ../error503.html'); 
   }

   //declaration of variables
   $treatmentPage = "/treatment/password_set_new.php";
   $originPage = "http://php-dev-1.online/password_reset.html";
   $userId = "";
   $password = "";
   $passwordRepeat = "";
   $passwordHashed = "";
   $passwordToken = "";
   $passwordTokenTime = "";
   $postToken = "";
   $sessionToken = "";
   $tokenTime = "";
   $timeLifeToken = time() - (15*60); //15 mins -- form and mail
   $errorMessage = "";

   $dataComplete = true; //to check if data send by from are complete
   $dataToProcess = true; //to check if data are good to be processed in db
   $deleteInDb = false;

   //check if all data are set and not null
    if(isset($_POST['password']) && !empty($_POST['password']) ){
        $password = htmlspecialchars($_POST['password']);
    }else{$dataComplete = false;}

    if(isset($_POST['password_repeat']) && !empty($_POST['password_repeat'])){
        $passwordRepeat = htmlspecialchars($_POST['password_repeat']);
    }else{$dataComplete = false;}
   
    if(isset($_SESSION['password_token']) && !empty($_SESSION['password_token'])){
        $passwordToken = htmlspecialchars($_SESSION['password_token']);
    }else{$dataComplete = false;}

    if(isset($_POST['token']) && !empty($_POST['token'])){
        $postToken = $_POST['token'];
    }else{$dataComplete = false;}

    if(isset($_SESSION['token']) && !empty($_SESSION['token'])){
        $sessionToken = $_SESSION['token'];
    }else{$dataComplete = false;}

    if(isset($_SESSION['token_time']) && !empty($_SESSION['token_time'])){
        $tokenTime = $_SESSION["token_time"];
    }else{$dataComplete = false;}
    

   //if all data continu treatment
   if($dataComplete){ 
       
        //check if url treatment is ok then return clean url if method is GET
        $checkedUrl = checkUrl($_SERVER['REQUEST_URI']);
        $clearUrl = checkUrl($_SERVER['HTTP_REFERER']);

        //check if origin url and url treatment
        if(($checkedUrl === $treatmentPage) && ($clearUrl== $originPage)){

            //search token password in db
            $sql = "SELECT iduser as id, password_date as pw_date FROM user where password_token = :pw_token";
            $sth = $connection->prepare($sql);
            $sth->bindParam(':pw_token', $passwordToken, PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);  

            if($result){
                $userId = $result["id"];
                $passwordTokenTime = $result["pw_date"];
                if($passwordTokenTime <= $timeLifeToken){  //check time life validity
                    $errorMessage .= "Vous avez dépassé le temps imparti pour la réinitialisation ! Veuillez recommencer votre demande.";
                    $dataToProcess = false;
                    $deleteInDb = true;//delete token in db
                }
            }else{ //token not found in db
                $errorMessage .= "Invalid token ! ";
                $dataToProcess = false;
            }

           //check if token CSRF (Cross-Site Request Forgery) is in form and already available
           if(!password_verify($postToken, $sessionToken) && $tokenTime <= $timeLifeToken){ 
               $dataToProcess = false;
               $errorMessage .= "Invalid token ! ";
           }

           if($password != $passwordRepeat){ //check if password and password_reapeat are == 
               $errorMessage .= 'Les mots de passe ne sont pas identiques ! '; 
               $dataToProcess = false;
           }else{ //passwords are == then check strength 
               if(!checkPassword($password)){ //not strong enough
                   $errorMessage .= 'Votre mot de passe doit comporter au minimum 8 caractères et contenir au moins un chiffre et une lettre majuscule ! '; 
                   $dataToProcess = false;
               }else{//strong enough then hash password
                   $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
               }
           }
           
        }else{ //urls are not correct
            session_destroy();
            header('HTTP/1.0 404 Not Found'); 
        }
    }else{ //data are not complete      
        $_SESSION["message"]["error"] = "Les données du formulaire ne sont pas complètes !"; 
        header('location: ../password_reset.html'); 
    }


    if(!$dataToProcess){ //data are not OK then send user on signup and display error
        if($deleteInDb === true){ //find token but time life past then delete it and send on login
            $passwordToken  = null;
            $passwordTokenTime = null;
            $sql = "UPDATE user SET password_token = :pw_token, password_date = :pw_date 
                    WHERE iduser = :id";
            $sth = $connection->prepare($sql);
            $sth->bindParam(':pw_token', $passwordToken, PDO::PARAM_STR);
            $sth->bindParam(':pw_date', $passwordTokenTime, PDO::PARAM_INT);
            $sth->bindParam(':id', $userId, PDO::PARAM_INT);
            $sth->execute();
            $sth->debugDumpParams();
            $_SESSION["message"]["error"] = $errorMessage;
            unset($_SESSION["token"]); 
            unset($_SESSION["token_time"]);
            header('location: ../index.html');
        }else{ //error not need to delete token in db
            $_SESSION["message"]["error"] = $errorMessage;
            header('location: ../password_reset.html');
        }
        
    } else{ //data are complete then insert new pw in data base and delete token
        $passwordToken  = null;
        $passwordTokenTime = 0;

        $sql = "UPDATE user SET user_pw = :pw, password_token = :pw_token, password_date = :pw_date 
                WHERE iduser = :id";
        $sth = $connection->prepare($sql);
        $sth->bindParam(':pw', $passwordHashed, PDO::PARAM_STR);
        $sth->bindParam(':pw_token', $passwordToken, PDO::PARAM_STR);
        $sth->bindParam(':pw_date', $passwordTokenTime, PDO::PARAM_INT);
        $sth->bindParam(':id', $userId, PDO::PARAM_INT);
        
        if($sth->execute()){// if data insert correctly then say to user  is ok and send on connexion page
            $_SESSION["message"]["success"] = "Votre mot de passe à bien été changé ! " ;
            unset($_SESSION["token"]); 
            unset($_SESSION["token_time"]);
            unset($_SESSION["message"]["error"]);
            header('location: ../index.html'); 
        }else{
            $_SESSION["message"]["error"] = " Une erreur s'est produite lors du changement de votre mot de passe ! "; 
            header('location: ../password_reset.html');
        }
    }
       

?>