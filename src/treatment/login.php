<?php

    require('functions_check.php');
    require('connection_db.php');

    //connection to session
    session_start(); 

   //connection db
    $connection = connectionDb();
    if(!$connection){echo $connection;}
    
    //declaration of variables
    $treatmentPage = "/treatment/login.php";
    $originPage = ["http://php-dev-1.online/","http://php-dev-1.online/index.html"];
    $iduser = "";
    $email = "";
    $password = "";
    $postToken = "";
    $sessionToken = "";
    $token_time = "";
    $errorMessage = "";

    $dataComplete = true; //to check if data send by from are complete
    $dataToProcess = true; //to check if data are good to be processed in db

    //check if all data are set and not null
    //set in intern variables and stock data to return in signup form with redirection
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

    //if all data continu treatment
    if($dataComplete){ 

        //check if url treatment is ok then return clean url if method is GET
        $checkedUrl = checkUrl($_SERVER['REQUEST_URI']);
        
        //refer is origin url and url treatment are signup and post
        if(($checkedUrl === $treatmentPage) 
        && (($_SERVER['HTTP_REFERER'] == $originPage[0]) || ($_SERVER['HTTP_REFERER'] == $originPage[1]))){

            //check if token CSRF (Cross-Site Request Forgery) is in form and already available
            //if token was generated into timestamp_old then is ok 
            //else if it was generated more than timestamp_old then it is too old and refused
            $timestamp_old = time() - (15*60); //15 mins
            if($sessionToken != $postToken && $token_time <= $timestamp_old){ //if session token == form token
                $dataToProcess = false;
                $errorMessage .= "Invalid token ! ";
            }
            
            //check mail format else return to the form
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && checkMail($email)){
                $sql = "SELECT count(user_email) as count FROM user WHERE user_email = :email";
                $sth = $connection->prepare($sql);
                $sth->bindParam(':email', $email, PDO::PARAM_STR);
                $sth->execute();
                $result = $sth->fetch(PDO::FETCH_ASSOC);
                if($result['count'] <= 0){ //check mail exist in db else return to the form
                    $errorMessage .= 'Les identifiants ne sont pas valides ! '; 
                    $dataToProcess = false;
                    header('location: ../index.html'); //redirection in signup and display error message
                }
            }else{
                $errorMessage .= 'Email invalide !';
                $dataToProcess = false;
                header('location: ../index.html'); //redirection in signup and display error message
            }

        }else{ //urls are not correct
            session_destroy();
            header('HTTP/1.0 404 Not Found'); 
        }
    }else{ //if data are not complete    
        $_SESSION["message"]["error"] = "Les données du formulaire ne sont pas complètes ! "; 
        header('location: ../index.html'); 
    }


    if(!$dataToProcess){
        $_SESSION["message"]["error"] = $errorMessage;
        header('location: ../index.html'); //redirection in signup and display error message
    }else{
        $sql = "SELECT iduser, user_name, user_email, user_pw FROM user WHERE user_email = :email";
        $sth = $connection->prepare($sql);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if($result && password_verify($password, $result["user_pw"])){
            $user =  ["id" => $result["iduser"], "name" => $result["user_name"]] ;
            $_SESSION["user"] = $user;  
            unset($_SESSION["email"]);
            unset($_SESSION["password"]);
            unset($_SESSION["token"]);
            unset($_SESSION["token_time"]);
            unset( $_SESSION["message"]);
            //for test : 
                $_SESSION['last_access'] = time();
            //
            header('location: ../dashbord.php'); 
        }else{
            $_SESSION["message"]["error"]  = "Les identifiants ne sont pas valides ! ";
            header('location: ../index.html'); //redirection in signup and display error message
        }       
    }
    
?>