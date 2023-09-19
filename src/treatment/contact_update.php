<?php
    
    require('check_functions.php');
    require('connection_db.php');

    //connection to session
    session_start(); 

    if(isset($_POST['btnCancel'])){ //user clicked on Cancel
        unset($_SESSION["contact"]);
        unset($_SESSION["token"]);
        unset($_SESSION["token_time"]);
        unset( $_SESSION["message"]);
        header('location: contact_display.php');   
    
    }else{ //user clicked validate

        //connection db
        $connection = connectionDb();
        if(!$connection){
            header('location: ../error503.html'); 
        }

        //declaration of variables
        $treatmentPage = "/treatment/contact_update.php";
        $originPage = "http://php-dev-1.online/dashboard.html";
        $userId = "";
        $contactId = "";
        $contact = "" ;
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

        if(isset($_POST['id_contact']) && !empty($_POST['id_contact'])){
            $contactId = htmlspecialchars($_POST['id_contact']);
            $_SESSION["contact"]["id"] = $contactId;
        }else{$dataComplete = false;}

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
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !checkMail($email)){  //check email format
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
            $sql = "UPDATE contact SET contact_name = :nom, contact_surname = :prenom, contact_email = :email 
                    WHERE idcontact = :id_contact";
            $sth = $connection->prepare($sql);
            $sth->bindParam(':nom', $name, PDO::PARAM_STR);
            $sth->bindParam(':prenom', $surname, PDO::PARAM_STR);
            $sth->bindParam(':email', $email, PDO::PARAM_STR);
            $sth->bindParam(':id_contact', $contactId, PDO::PARAM_INT);
            if($sth->execute()){ //check insertion of new contact
                $_SESSION["message"]["success"]  = "Votre contact à bien été modifié." ;
                unset($_SESSION["contact"]);
                unset($_SESSION["token"]);
                unset($_SESSION["token_time"]);
                unset( $_SESSION["message"]["error"]);
                header('location: contact_display.php');   
            }else{
                $_SESSION["message"]["error"] = "Une erreur est survenu lors de la modification sur le contact !";  
                header('location: contact_display.php'); 
            }
        }
    }
?>