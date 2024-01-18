<?php
    //connection with db 
    function connectionDb(){
        $dsn = 'mysql:host=mysql;dbname=' . getenv('MYSQL_DATABASE') . ';charset=utf8';  // host = name service
        $user = getenv('MYSQL_USER'); //$_ENV['MYSQL_USER'] may not work with some environments
        $pw = getenv('MYSQL_PASSWORD');
        
        try{ //try to connect
                $conn = new PDO($dsn, $user, $pw);
                //define error mode in PDO on exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $conn;
        }catch(PDOException $e){ //catch exception and get its informations
                // echo json_encode($e); 
                header('location: ../error503.html'); 
        }
    }

?>