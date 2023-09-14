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
    $tokenTime = "";
    $timeLifeToken = time() - (15*60); //15 mins
    $rememberToken = "";
    $rememberTime = "";
    $rememberTimeStr = "";
    $rememberTokenHash = "";
    $rememberTimeHash = "";
    $userRemember = false;
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
        $tokenTime = $_SESSION["token_time"];
    }else{$dataComplete = false;}

    if(isset($_POST['remember_me']) && !empty($_POST['remember_me'])){      
        $rememberToken = uniqid(rand(), true);
        $rememberTime = time();
    }

    if($dataComplete){  //if all data continu treatment 

        //check if url treatment is ok then return clean url if method is GET
        $checkedUrl = checkUrl($_SERVER['REQUEST_URI']);
        
        //check if origin url and url treatment
        if(($checkedUrl === $treatmentPage) 
        && (($_SERVER['HTTP_REFERER'] == $originPage[0]) || ($_SERVER['HTTP_REFERER'] == $originPage[1]))){

            //check if token CSRF (Cross-Site Request Forgery) is in form and already available
            if(!password_verify($postToken, $sessionToken) && $tokenTime <= $timeLifeToken){ 
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

            if($rememberToken != ""){  //remember is set then set token
                $rememberTokenHash = password_hash($rememberToken, PASSWORD_DEFAULT);
                $rememberTimeStr = strval($rememberTime);
                $sql = "UPDATE user SET remember_token = :token, remember_date = :tokentime WHERE iduser=:id"; 
                $sth = $connection->prepare($sql);
                $sth->bindParam(':token', $rememberTokenHash, PDO::PARAM_STR);
                $sth->bindParam(':tokentime', $rememberTimeStr, PDO::PARAM_STR);
                $sth->bindParam(':id', $result["iduser"], PDO::PARAM_INT);
                $sth->execute();

                if($sth->rowCount() > 0){ //if update is ok then set token in session to process it after with js
                    //hash time for second verification in automatic connection 
                    $rememberTimeHash = password_hash($rememberTime, PASSWORD_DEFAULT);
                    $_SESSION['remember']['token'] = $rememberTokenHash;
                    $_SESSION['remember']['time'] = $rememberTimeHash;
                    $userRemember = true;
                }else{//some error with update
                    $_SESSION["message"]["error"] = "Une erreur s'est produite lors de l'enregistrement de votre choix : remember me ! ";
                }

            }else{ //remember is not set
                
                //check if user have some old token set in DB and delete it
                $sql = "SELECT count(remember_token) as count FROM user WHERE iduser = :id"; 
                $sth = $connection->prepare($sql);
                $sth->bindParam(':id', $result["iduser"], PDO::PARAM_INT);
                $sth->execute();
                $resultRemember = $sth->fetch(PDO::FETCH_ASSOC);

                if($resultRemember["count"] > 0){  // then delete token in db
                    
                    $sql = "UPDATE user SET remember_token = :token, remember_date = :tokentime WHERE iduser=:id"; 
                    $sth = $connection->prepare($sql);
                    $sth->bindParam(':token', $rememberToken, PDO::PARAM_STR);
                    $sth->bindParam(':tokentime', $rememberTime, PDO::PARAM_STR);
                    $sth->bindParam(':id', $result["iduser"], PDO::PARAM_INT);
                    $sth->execute();

                    if($sth->rowCount() == 0){//some error with update
                        echo json_encode("error on delete values of remember token in db");
                    }

                }
            }
            if($userRemember){
                $user =  ["id" => $result["iduser"], "name" => $result["user_name"], 'remember' => $userRemember];
            }else{
                $user =  ["id" => $result["iduser"], "name" => $result["user_name"]];
            }
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

