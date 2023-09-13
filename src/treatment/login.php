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
    $treatmentPage = "/treatment/login.php";
    $originPage = ["http://php-dev-1.online/","http://php-dev-1.online/index.html"];
    $user = "";
    $email = "";
    $password = "";
    $postToken = "";
    $sessionToken = "";
    $token_time = "";
    $rememberMe = "";
    $errorMessage = "";

    $dataComplete = true; //to check if data send by from are complete
    $dataToProcess = true; //to check if data are good to be processed in db

    //check if all data are set and not null
    //set them in intern variables and stock data to return in signup form with redirection
    if(isset($_POST['email']) && !empty($_POST['email'])){
        $email = htmlspecialchars($_POST['email']);
        $_SESSION["email"] = $email;
    }else{$dataComplete = false;}
    
    if(isset($_POST['password']) && !empty($_POST['password'])){
        $password = htmlspecialchars($_POST['password']);
        $_SESSION["password"] = $password;
    }else{$dataComplete = false;}

    if(isset($_POST['token']) && !empty($_POST['token'])){
        $postToken = $_POST['token'];
    }else{$dataComplete = false;}

    if(isset($_SESSION['token']) && !empty($_SESSION['token'])){
        $sessionToken = $_SESSION['token'];
    }else{$dataComplete = false;}

    if(isset($_SESSION['token_time']) && !empty($_SESSION['token_time'])){
        $token_time = $_SESSION["token_time"];
    }else{$dataComplete = false;}

    if(isset($_POST['remember_me']) && !empty($_POST['remember_me'])){      
        $rememberMe = uniqid(rand(), true);
    }

   
    if($dataComplete){  //if all data continu treatment 

        //check if url treatment is ok then return clean url if method is GET
        $checkedUrl = checkUrl($_SERVER['REQUEST_URI']);
        
        //check if origin url and url treatment
        if(($checkedUrl === $treatmentPage) 
        && (($_SERVER['HTTP_REFERER'] == $originPage[0]) || ($_SERVER['HTTP_REFERER'] == $originPage[1]))){

            //check if token CSRF (Cross-Site Request Forgery) is in form and already available
            //if token was generated into timestamp_old then is ok 
            //else if it was generated more than timestamp_old then it is too old and refused
            $timestamp_old = time() - (15*60); //15 mins
            if(!password_verify($postToken, $sessionToken) && $token_time <= $timestamp_old){ 
                $dataToProcess = false;
                $errorMessage .= "Invalid token ! ";
            }
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && checkMail($email)){ //check mail format 
                //check mail exist in db 
                $sql = "SELECT count(user_email) as count FROM user WHERE user_email = :email";
                $sth = $connection->prepare($sql);
                $sth->bindParam(':email', $email, PDO::PARAM_STR);
                $sth->execute();
                $result = $sth->fetch(PDO::FETCH_ASSOC);
                
                if($result['count'] <= 0){ //mail do not exist
                    $errorMessage .= 'Les identifiants ne sont pas valides ! '; 
                    $dataToProcess = false;
                }
            }else{ //mail format is not OK
                $errorMessage .= 'Email invalide !';
                $dataToProcess = false;
            }

        }else{ //urls are not correct
            session_destroy();
            header('HTTP/1.0 404 Not Found'); 
        }
    }else{ //data are not complete    
        $_SESSION["message"]["error"] = "Les données du formulaire ne sont pas complètes ! "; 
        header('location: ../index.html'); 
    }

   
    if(!$dataToProcess){  //data are not OK then send user on login and display error
        $_SESSION["message"]["error"] = $errorMessage;
        header('location: ../index.html'); 
    }else{ //data are OK
        //search user by email
        $sql = "SELECT iduser, user_name, user_surname, user_pw FROM user WHERE user_email = :email";
        $sth = $connection->prepare($sql);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if($result > 0 && password_verify($password, $result["user_pw"])){//email is found then verify password
           
            if($rememberMe != ""){  //if remember is set then set token in db
                $rememberHash = password_hash($rememberMe, PASSWORD_DEFAULT);
                $sql = "UPDATE user SET user_remember = :remember WHERE iduser = :id";
                $sth = $connection->prepare($sql);
                $sth->bindParam(':remember', $rememberHash, PDO::PARAM_STR);
                $sth->bindParam(':id', $result["iduser"], PDO::PARAM_INT);
                $sth->execute();

                if($sth->rowCount() > 0){ //if update is ok then set token in session to process it after with js
                    $_SESSION['remember_me'] = $rememberHash;
                }else{ //some error with update
                    $_SESSION["message"]["error"] = "Une erreur s'est produite lors de l'enregistrement de votre choix : remember me ! ";
                }
                
            }else{ //check if user have some old token set in DB
                $sql = "SELECT count(user_remember) FROM user WHERE iduser = :id"; 
                $sth = $connection->prepare($sql);
                $sth->bindParam(':id', $result["iduser"], PDO::PARAM_INT);
                $sth->execute();
                $resultRemember = $sth->fetch(PDO::FETCH_ASSOC);
                
                if($resultRemember > 0){ //old token was found then delete it
                    $sql = "UPDATE user SET user_remember = :remember WHERE iduser = :id";
                    $sth = $connection->prepare($sql);
                    $sth->bindParam(':remember', $rememberMe, PDO::PARAM_STR);
                    $sth->bindParam(':id', $result["iduser"], PDO::PARAM_INT);
                    $sth->execute();
                    if($sth->rowCount() < 0){
                       echo json_encode("error on delete old token");
                    }
                }
            }

            $user =  ["id" => $result["iduser"], "name" => $result["user_name"]] ;
            $_SESSION["user"] = $user;  
            unset($_SESSION["email"]);
            unset($_SESSION["password"]);
            unset($_SESSION["token"]);
            unset($_SESSION["token_time"]);
            header('location: contact_display.php'); 

        }else{ //pw not match with user then send user on login and display error
            $_SESSION["message"]["error"]  = "Les identifiants ne sont pas valides ! ";
            header('location: ../index.html'); 
        }       
    }
    
?>

