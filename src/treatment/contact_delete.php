<?php
    require('connection_db.php');

    //declaration of variables 
    $data = "";
    $contactToDelete = "";
    $deleted = false;

    //get data from js localstorage 
    $data = json_decode(file_get_contents('php://input'));

    // if data are not null then process them
    if($data != null){
        $contactToDelete = $data->idcontact;

        if(count($contactToDelete) > 0){
            // connection to session
            session_start(); 

            // connection db
            $connection = connectionDb();
            if(!$connection){
                echo json_encode('error503.html'); 
            }
            
            //check if user session exist
            if(isset($_SESSION["user"]["id"]) && !empty($_SESSION["user"]["id"])){
                
                foreach ($contactToDelete as $key => $value) { //delete contact(s) in db
                    $sql = "DELETE FROM contact 
                            WHERE idcontact = :id_contact";
                    $sth = $connection->prepare($sql);
                    $sth->bindParam(':id_contact', $value, PDO::PARAM_INT);
                    $sth->execute();

                    if($sth->rowCount() > 0){
                        $deleted = true;
                    }else{
                        $deleted = false;
                    }
                }

                if($deleted == true){
                    $_SESSION["message"]["success"] = "Le(s) contact(s) a/ont bien été supprimé(s).";
                }else{
                    $_SESSION["message"]["error"] = "Une erreur s'est produite lors de la suppression !";
                }
                echo json_encode($deleted);

            }else{
                $_SESSION["message"]["error"] = "Une erreur s'est produite ! Merci de vous reconnecter !";
                echo json_encode("No user session");
            }
        }else{
            $_SESSION["message"]["error"] = "Une erreur s'est produite lors de la suppression !";
            echo json_encode("No data to delete");
        }
        
    }else{
        $_SESSION["message"]["error"] = "Une erreur s'est produite lors de la suppression !";
        echo json_encode("No data to delete");
    }

?>