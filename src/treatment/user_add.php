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
    $treatmentPage = "/treatment/user_add.php";
    $originPage = "http://php-dev-1.online/signup.html";
    $name = "";
    $surname = "";
    $email = "";
    $password = "";
    $passwordRepeat = "";
    $passwordHashed = "";
    $postToken = "";
    $sessionToken = "";
    $tokenTime = "";
    $timeLifeToken = time() - (15*60); //15 mins
    $errorMessage = "";

    $dataComplete = true; //to check if data send by from are complete
    $dataToProcess = true; //to check if data are good to be processed in db

    //check if all data are set and not null
    if(isset($_POST['prenom']) && !empty($_POST['prenom'])){
        $name = htmlspecialchars($_POST['prenom']);
        $_SESSION["user"]["name"] = $name;
    }else{$dataComplete = false;}
    
    if(isset($_POST['nom']) && !empty($_POST['nom'])){
        $surname = htmlspecialchars($_POST['nom']);
        $_SESSION["user"]["surname"] = $surname;
    }else{$dataComplete = false;}
    
    if(isset($_POST['email']) && !empty($_POST['email'])) {
        $email = htmlspecialchars($_POST['email']);
        $_SESSION["user"]["email"] = $email;
    }else{$dataComplete = false;}
    
    if(isset($_POST['password']) && !empty($_POST['password']) ){
        $password = htmlspecialchars($_POST['password']);
        $_SESSION["user"]["password"] = $password;
    }else{$dataComplete = false;}

    if(isset($_POST['password_repeat']) && !empty($_POST['password_repeat'])){
        $passwordRepeat = htmlspecialchars($_POST['password_repeat']);
        $_SESSION["user"]["password_repeat"] = $passwordRepeat;
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

        //check if origin url and url treatment
        if(($checkedUrl === $treatmentPage) && ($_SERVER['HTTP_REFERER'] == $originPage)){

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
                    $errorMessage .= 'Votre mot de passe doit comporter au minimum 8 caractères et contenir au moins un chiffre et une lettre majuscule ! '; //stock data in session 
                    $dataToProcess = false;
                }else{//strong enough then hash password
                    $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
                }
            }
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && checkMail($email)){ //check email format
                //check mail exist in db 
                $sql = "SELECT count(user_email) as count FROM user WHERE user_email = :email";
                $sth = $connection->prepare($sql);
                $sth->bindParam(':email', $email, PDO::PARAM_STR);
                $sth->execute();
                $result = $sth->fetch(PDO::FETCH_ASSOC);

                if($result['count'] > 0){ //mail already exist in db
                    $errorMessage .= 'Email invalide ! '; 
                    $dataToProcess = false;
                }
            }else{ //mail format is not OK
                $errorMessage .= 'Email invalide ! '; 
                $dataToProcess = false;
            }
        }else{ //urls are not correct
            session_destroy();
            header('HTTP/1.0 404 Not Found'); 
        }
    }else{ //data are not complete      
        $_SESSION["message"]["error"] = "Les données du formulaire ne sont pas complètes !"; 
        header('location: ../signup.html'); 
    }


    if(!$dataToProcess){ //data are not OK then send user on signup and display error
        $_SESSION["message"]["error"] = $errorMessage;
        header('location: ../signup.html');
    } else{ //data are complete, mail do not exist and passwords are the same then insert in data base
        $sql = "INSERT INTO user(user_name, user_surname, user_email, user_pw) 
                VALUES (:prenom, :nom, :email, :pw)";
        $sth = $connection->prepare($sql);
        $sth->bindParam(':prenom', $name, PDO::PARAM_STR);
        $sth->bindParam(':nom', $surname, PDO::PARAM_STR);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->bindParam(':pw', $passwordHashed, PDO::PARAM_STR);
        
        if($sth->execute()){// if data insert correctly then say to user sign up is ok and send on connexion page
            $_SESSION["message"]["success"] = "Votre inscription à bien été prise en compte ! " ;
            unset($_SESSION["user"]); 
            unset($_SESSION["message"]["error"]);
            header('location: ../index.html'); 
        }else{
            $_SESSION["message"]["error"] = " Une erreur s'est produite lors de votre inscription ! "; 
            header('location: ../signup.html');
        }
    }
        
?>

 