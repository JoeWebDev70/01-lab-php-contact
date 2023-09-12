<?php
    
    global $urlTreatment;

    $GLOBALS['urlTreatment'] = [
        '/treatment/login.php',
    ];

    //check if url and method are ok - return url 
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


    function checkMail($email){
        $pattern = "/^([a-zA-Z0-9\._\-]+)@([a-zA-Z0-9\-]+)(?:\.[a-zA-Z0-9\-]+)*$/";
        if(preg_match($pattern, $email)){
            return true;
        }else{
            return false;
        }
    }

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