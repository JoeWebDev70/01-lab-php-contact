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
    $treatmentPage = "/treatment/password_mail.php";
    $originPage = "http://php-dev-1.online/password.html";
    $name = "";
    $email = "";
    $postToken = "";
    $sessionToken = "";
    $tokenTime = "";
    $timeLifeToken = time() - (15*60); //15 mins
    $errorMessage = "";
    $passwordToken = "";
    $passwordTokenHash = "";
    $passwordTokenTime = "";

    $dataComplete = true; //to check if data send by from are complete
    $dataToProcess = true; //to check if data are good to be processed in db

    //check if all data are set and not null
    if(isset($_POST['email']) && !empty($_POST['email'])){
        $email = htmlspecialchars($_POST['email']);
        $_SESSION["email"] = $email;
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


    if($dataComplete){  //if all data continu treatment 

        //check if url treatment is ok then return clean url if method is GET
        $checkedUrl = checkUrl($_SERVER['REQUEST_URI']);

        //check if origin url and url treatment
        if(($checkedUrl === $treatmentPage) && (($_SERVER['HTTP_REFERER'] == $originPage))){
            
            //check if token CSRF (Cross-Site Request Forgery) is in form and already available
            if(!password_verify($postToken, $sessionToken) && $tokenTime <= $timeLifeToken){ 
                $dataToProcess = false;
                $errorMessage .= "Invalid token ! ";
            }
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && checkMail($email)){ //check mail format 
                //check mail exist in db 
                $sql = "SELECT user_email as email, user_name as 'name' FROM user WHERE user_email = :email";
                $sth = $connection->prepare($sql);
                $sth->bindParam(':email', $email, PDO::PARAM_STR);
                $sth->execute();
                $result = $sth->fetch(PDO::FETCH_ASSOC);

                if(!$result){ //mail do not exist
                    $errorMessage .= 'Email invalide ! '; 
                    $dataToProcess = false;
                }
                else{
                    $name = $result['name'];
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
        header('location: ../password.html'); 
    }else{ //data are OK

        //generate token for changing password and with time stamp
        $passwordToken = uniqid(rand(), true);
        $passwordTokenHash = password_hash($passwordToken, PASSWORD_DEFAULT);
        $passwordTokenTime = time();

        //set token and time in db
        $sql = "UPDATE user SET password_token = :token, password_date = :token_time WHERE user_email = :email";
        $sth = $connection->prepare($sql);
        $sth->bindParam(':token', $passwordTokenHash, PDO::PARAM_STR);
        $sth->bindParam(':token_time', $passwordTokenTime, PDO::PARAM_STR);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);

        
        if($sth->execute()){//insertion in db ok then send mail to user

            $to = $email;
            $from = "monannuaire@monannuaire.com";
            $subject = "Récupération de password";
            $introduction = "Bonjour " . $name . ",";
            $bodyPart1 = "Nous avons réinitialisé votre compte monannuaire. Suivez les instructions ci-dessous si vous avez émis cette demande.";
            $bodyPart2 = "Ignorez cet e-mail si la demande de réinitialisation de votre mot de passe n'a pas été déposée par vous. Ne vous inquiétez pas, votre compte est toujours sécurisé.";
            $bodyPart3 = "Cliquez sur le lien suivant pour définir un nouveau mot de passe.";
            $linkReset = "http://php-dev-1.online/password_reset.html?token=".$passwordTokenHash;
            $bodyPart4 = "Si l'activation ne fonctionne pas après avoir cliqué sur le bouton, vous pouvez copier le lien dans votre fenêtre de navigateur ou le saisir directement.";
            $thanks = "Sincères salutations,";
            $signature = "Votre équipe monannuaire.";
            $linksite = "http://php-dev-1.online/";

            $email_message = '<head>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                <style> #btnlink{color:white; text-decoration:none;} button{margin-left:300px;}
                    .margin_b{margin-bottom: 30px;} .margin_t{margin-top : 30px;}
                </style>
                </head>';
            $email_message .= '<p class="margin_t margin_b">' . $introduction . '</p>';
            $email_message .= '<p>' . $bodyPart1 . '</p>';
            $email_message .= '<p>' . $bodyPart2 . '</p>';
            $email_message .= '<p>' . $bodyPart3 . '</p>';
            $email_message .= '<button type="submit" class="btn btn-primary"><a id="btnlink" href="' . $linkReset . '">Réinitialisation</button>';
            $email_message .= '<p class="margin_t margin_b"><a href="' . $linkReset . '">' . $linkReset . '</a></p>';
            $email_message .= '<p>' . $bodyPart4 . '</p>';
            $email_message .= '<p class="margin_t">' . $thanks . '</p>';
            $email_message .= '<p class="margin_b">' . $signature . '</p>' ;
            $email_message .= '<a href="'. $linksite . '">monannuaire@monannuaire.com</a>' . "\r\n";

            $headers = 'From: ' . $from . "\r\n";
            $headers .= 'To: ' . $to . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=utf-8\r\n";
            $headers .= "\r\n";

            if(mail($to, $subject, $email_message, $headers)){ 
                $_SESSION["message"]["success"] = "Un mail vous a été envoyer. Merci de suivre les instructions ! ";
                unset($_SESSION["email"]);
                unset($_SESSION["token"]);
                unset($_SESSION["token_time"]);
                unset($_SESSION["message"]["error"]);
                header('location: ../index.html'); 
            }else{//send user on login page and display error
                $_SESSION["message"]["error"]  = "Une erreur s'est produite lors de l'envois du mail ! ";
                header('location: ../index.html'); 
            }
        } 
    }

?>