<?php

    $to = "test@gmail.com";
    $from = "monsite@monsite.com";
    $subject = "password";
    $message = "reset password";

    $email_message = '<h1>' . $subject . '</h1>';
    $email_message .= '<p>' . $message . '</p>' . "\r\n";


    $headers = 'From: ' . $from . "\r\n";
    $headers = 'To: ' . $to . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    $headers .= "\r\n";

    if(mail($to, $subject, $email_message, $headers)){ 
        header('location: index.html'); //renvoi sur page index
    }else{
        echo 'Soit hébergement ne va pas bien soit on ne sait pas coder, aller voir le concurrent ';
    }


?>