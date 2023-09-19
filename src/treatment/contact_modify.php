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
    $treatmentPage = "/treatment/contact_modify.php";
    $originPage = "http://php-dev-1.online/dashboard.html";
    $userId = "";
    $contactId = "";
    $contact = "" ;

    //check if all data are set and not null
    if(isset($_SESSION["user"]["id"]) && !empty($_SESSION["user"]["id"])){
        $userId = htmlspecialchars($_SESSION["user"]["id"]);
    }else{
        header('location: logout.php');
    }

    if(isset($_GET['id_contact']) && !empty($_GET['id_contact'])){
        $contactId = $_GET['id_contact'];
    }
     
    //check if url treatment is ok then return clean url if method is GET
    $checkedUrl = checkUrl($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

    //check if origin url and url treatment
    if(($checkedUrl === $treatmentPage) && $_SERVER['HTTP_REFERER'] == $originPage){
        //search for contact informations to display it in form
        $sql = 'SELECT c.idcontact AS "id", c.contact_name AS "name", c.contact_surname AS "surname", c.contact_email AS "email" 
                FROM contact as c WHERE user_iduser = :id AND c.idcontact = :id_contact';               
                $sth = $connection->prepare($sql);
                $sth->bindParam(':id', $userId, PDO::PARAM_INT);
                $sth->bindParam(':id_contact', $contactId, PDO::PARAM_INT);
                $sth->execute();
                $result = $sth->fetch(PDO::FETCH_ASSOC);

                if($result > 0){ //contact informations found
                    $_SESSION["contact"] = $result;
                    header('location: contact_display.php'); 
                }else{
                    $_SESSION["message"]["error"] = "Une erreur s'est produite lors de la récupération des données ! ";  
                    header('location: contact_display.php'); 
                }
    }else{ //urls are not correct
        session_destroy();
        header('HTTP/1.0 404 Not Found'); 
    }
    
?>