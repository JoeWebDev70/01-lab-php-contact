<?php

    require('check_functions.php');
    require('connection_db.php');
    
    //connection to session
    session_start(); 

   //connection db
    $connection = connectionDb();

    //declaration of variables
    $treatmentPage = "/treatment/contact_display.php";
    $originPage = ["http://php-dev-1.online/", "http://php-dev-1.online/index.html","http://php-dev-1.online/dashboard.html"]; 
    $userId = "";

    //check if user session exist
    if(isset($_SESSION["user"]["id"]) && !empty($_SESSION["user"]["id"])){
        $userId = $_SESSION["user"]["id"];
    }else{
        header('location: ../treatment/logout.php');
    }

    //check if url treatment is ok then return clean url if method is GET
    $checkedUrl = checkUrl($_SERVER['REQUEST_URI']);

    if(($checkedUrl === $treatmentPage) && 
    (($_SERVER['HTTP_REFERER'] == $originPage[0]) || ($_SERVER['HTTP_REFERER'] == $originPage[1])
    || ($_SERVER['HTTP_REFERER'] == $originPage[2]))){ 

        $sql = 'SELECT c.idcontact AS "id", c.contact_name AS "name", c.contact_surname AS "surname", c.contact_email AS "email" 
                FROM contact as c WHERE user_iduser = :id ORDER BY surname ASC';
        $sth = $connection->prepare($sql);
        $sth->bindParam(':id', $userId, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC); 
        $_SESSION["contacts"] = $result; //set in session also if user doesn't have contact
        $_SESSION['last_access'] = time();
        header('location: ../dashboard.html');
    }else{ //urls are not correct
        session_destroy();
        header('HTTP/1.0 404 Not Found'); 
    }


?>