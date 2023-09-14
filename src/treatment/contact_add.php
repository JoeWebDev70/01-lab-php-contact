<?php

    require('check_functions.php');
    require('connection_db.php');

    //connection to session
    session_start(); 

    //connection db
    $connection = connectionDb();
    if(!$connection){
        header('location: error503.html'); 
    }

    //declaration of variables
    $treatmentPage = "/treatment/contact_add.php";
    $originPage = "http://php-dev-1.online/dashbord.html";
    $userId = "";
    $name = "";
    $surname = "";
    $email = "";
    $postToken = "";
    $sessionToken = "";
    $tokenTime = "";
    $timeLifeToken = "";
    $errorMessage = "";

    $dataComplete = true; //to check if data send by from are complete
    $dataToProcess = true; //to check if data are good to be processed in db
    
    //check if all data are set and not null
    if(isset($_SESSION["user"]["id"]) && !empty($_SESSION["user"]["id"])){
        $userId = htmlspecialchars($_SESSION["user"]["id"]);
    }else{
        header('location: logout.php');
    }

    if(isset($_SESSION["user"]["remember"]) && !empty($_SESSION["user"]["remember"])){
        $timeLifeToken = time() - (8*60*60); // 8h 
    }else{
        $timeLifeToken = time() - (10*60); // 10 mins like session  
    }

    if(isset($_POST['prenom']) && !empty($_POST['prenom'])){
        $name = htmlspecialchars($_POST['prenom']);
        $_SESSION["contact"]["name"] = $name;
    }else{$dataComplete = false;}
    
    if(isset($_POST['nom']) && !empty($_POST['nom'])){
        $surname = htmlspecialchars($_POST['nom']);
        $_SESSION["contact"]["surname"] = $surname;
    }else{$dataComplete = false;}

    if(isset($_POST['email']) && !empty($_POST['email'])){
        $email = htmlspecialchars($_POST['email']);
        $_SESSION["contact"]["email"] = $email;
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


    if($dataComplete){ //if all data continu treatment

        //check if url treatment is ok then return clean url if method is GET
        $checkedUrl = checkUrl($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

        //check if origin url and url treatment
        if(($checkedUrl === $treatmentPage) && $_SERVER['HTTP_REFERER'] == $originPage){

            //check if token CSRF (Cross-Site Request Forgery) is in form and already available
            if($sessionToken != $postToken && $tokenTime <= $timeLifeToken){ 
                $dataToProcess = false;
                $errorMessage .= "Invalid token ! ";
            }
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && checkMail($email)){  //check email format
                //check if email exist in db for this user
                $sql = 'SELECT c.contact_email AS "email" 
                        FROM contact as c WHERE user_iduser = :id AND c.contact_email = :email';               
                $sth = $connection->prepare($sql);
                $sth->bindParam(':id', $userId, PDO::PARAM_INT);
                $sth->bindParam(':email', $email, PDO::PARAM_STR);
                $sth->execute();
                $result = $sth->fetch(PDO::FETCH_ASSOC);

                if($result > 0){ //if mail already exist in user contact
                    $errorMessage .= 'Le contact : ' . $result['email'] .' est déjà enregistré ! ' ;
                    $dataToProcess = false;
                }
            }else{
                $errorMessage .= 'Email invalide !'; 
                $dataToProcess = false;
            }

        }else{ //urls are not correct
            session_destroy();
            header('HTTP/1.0 404 Not Found'); 
        }
    }else{ //if data are not complete    
        $_SESSION["message"]["error"] = "Les données du formulaire ne sont pas complètes ! ";  
        header('location: contact_display.php'); 
    }
         

    if(!$dataToProcess){ //data are complete but not OK
        $_SESSION["message"]["error"]  = $errorMessage;
        header('location: contact_display.php'); 
    } else{ //data are complete, mail do not exist then insert in data base
        $sql = "INSERT INTO contact(contact_name, contact_surname, contact_email, user_iduser) 
                VALUES (:prenom, :nom, :email, :iduser)";
        $sth = $connection->prepare($sql);
        $sth->bindParam(':prenom', $name, PDO::PARAM_STR);
        $sth->bindParam(':nom', $surname, PDO::PARAM_STR);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->bindParam(':iduser', $userId, PDO::PARAM_INT);
        $sth->execute();

        if($sth->rowCount() > 0){ //check insertion of new contact
            $_SESSION["message"]["success"]  = "Votre contact à bien été enregistré." ;
            unset($_SESSION["contact"]);
            unset($_SESSION["token"]);
            unset($_SESSION["token_time"]);
            unset( $_SESSION["message"]["error"]);
            header('location: contact_display.php');   
        }else{
            $_SESSION["message"]["error"] = " Une erreur s'est produite lors de l'insertion du contact ";  
            header('location: contact_display.php'); 
        }
    }

?>