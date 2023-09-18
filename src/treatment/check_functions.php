<?php
    
    // declaration of url to check it before processing data
    global $urlTreatment;

    $GLOBALS['urlTreatment'] = [
        '/treatment/login.php',
        '/treatment/user_add.php',
        '/treatment/contact_display.php',
        '/treatment/contact_add.php',
        '/treatment/contact_modify.php',
        '/treatment/contact_update.php',
        '/treatment/contact_confirm_delete.php',
        '/treatment/contact_delete.php',
        '/treatment/password_mail.php',
        '/treatment/password_set_new.php',
        'http://php-dev-1.online/password_reset.html',

    ];

    //check if url is in array and return clean url for method GET
    function checkUrl($url){
        foreach($GLOBALS['urlTreatment'] as $pattern){
            $urlSplit = explode("?", $url);     //when method is get then need return just the first url part
            $pattern = str_replace("/", "\/", $pattern);
            if(preg_match("/^" . $pattern . "$/", $urlSplit[0], $matches)){
                return $urlSplit[0];
            }
        }
        return false;
    }

    //additional check for see if mail is in correct format
    function checkMail($email){
        $pattern = "/^([a-zA-Z0-9\._\-]+)@([a-zA-Z0-9\-]+)(?:\.[a-zA-Z0-9\-]+)*$/";
        if(preg_match($pattern, $email)){
            return true;
        }else{
            return false;
        }
    }

    // check if password is strong enough
    function checkPassword($passwordValue){
        $pwFormatStrength = "/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/"; 
        //min 8 char max 20 - min 1 number and 1 uppercase char
        if(preg_match($pwFormatStrength, $passwordValue)){
            return true;
        }else{
            return false;
        }
    }


?>