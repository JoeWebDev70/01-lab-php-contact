<?php
    session_start();

    //declaration of variables
    $email = "";
    $token = "";
    $successMessage = "";
    $errorMessage = "";
    $message = "";
    $class = "";
    
    $token = uniqid(rand(), true); //token creation 
    $_SESSION['token'] = password_hash($token, PASSWORD_DEFAULT); //store in session
    $_SESSION['token_time'] = time(); //store token timestamp in session

    //get the values to set in form
    if(isset($_SESSION["email"]) && !empty($_SESSION["email"])){
        $email = $_SESSION["email"];
        unset($_SESSION["email"]);
    }

    //get the values for display messages
    if(isset($_SESSION["message"]) && !empty($_SESSION["message"])){
        if(isset($_SESSION["message"]["error"])){$errorMessage = $_SESSION["message"]["error"];}
        if(isset($_SESSION["message"]["success"])){$successMessage = $_SESSION["message"]["success"];}
        unset($_SESSION["message"]);
    }

    if(isset($successMessage) && !empty($successMessage)){
        $message = $successMessage;
        $class = "alert-success";
        $successMessage = "";
    }else if(isset($errorMessage) && !empty($errorMessage)){
        $message = $errorMessage;
        $class = "alert-danger";
        $errorMessage = "";
    }
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récupération password</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="./treatment/alert_message.js" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="./treatment/logout.php">Mon Annuaire</a>
</nav>

<div class="container mt-5">
    <h1>Récupération password</h1>
            
    <!-- message display -->
    <div class="alert <?php echo $class ?>" role="alert" style="visibility: hidden; min-height: 50px"> 
        <?php if(isset($message) && !empty($message)){ ?>
            <p id="<?php echo $message;?>" style="margin-bottom: 0;"><?php echo $message;?> </p> 
        <?php $message = ""; } ?>
    </div> 
        
    <!-- if error message display -->
    <div class="alert alert-danger" role="alert" style="display: none;"> 
        <?php if(isset($errorMessage) && !empty($errorMessage)){ ?>
            <p id="<?php echo $errorMessage;?>" style="margin-bottom: 0;"><?php echo $errorMessage;?> </p> 
        <?php $errorMessage = ""; } ?>
    </div> 

    <form action="./treatment/password_mail.php" method="POST" enctype=multipart/form-data>
    
        <!-- Champ : Adresse e-mail -->
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo $email; ?>">
        </div>

        <!-- token hidden in form -->
        <input type="hidden" name="token" id="token" value="<?php echo $token; ?>">

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>
</html>
