<?php
    session_start(); //https://www.php.net/manual/fr/reserved.variables.session.php

    //declaration of variables
    $prenom = "";
    $nom = "";
    $email = "";
    $password = "";
    $passwordRepeat = "";
    $errorMessage = "";
    $token = "";
    $messageLifeTime = 10; //10 sec
    $messageTime = "";

    $token = uniqid(rand(), true); //token creation
    $_SESSION['token'] = password_hash($token, PASSWORD_DEFAULT); //store in session
    $_SESSION['token_time'] = time(); //store token timestamp in session
    
    // get the values to set in form
    if(isset($_SESSION["user"]) && !empty($_SESSION["user"])){ 
        if(isset($_SESSION["user"]["name"])){$prenom = $_SESSION["user"]["name"];}
        if(isset($_SESSION["user"]["surname"])){$nom = $_SESSION["user"]["surname"];}
        if(isset($_SESSION["user"]["email"])){$email = $_SESSION["user"]["email"];}
        if(isset($_SESSION["user"]["password"])){$password = $_SESSION["user"]["password"];}
        if(isset($_SESSION["user"]["password_repeat"])){$passwordRepeat = $_SESSION["user"]["password_repeat"];}
        unset($_SESSION["user"]);
    }

    // get the values for display messages
    if(isset($_SESSION["message"]) && !empty($_SESSION["message"])){
        if(isset($_SESSION["message"]["error"])){
            $errorMessage = $_SESSION["message"]["error"];
            unset($_SESSION["message"]["error"]);
        } 
    }
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="./treatment/alert_message.js" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="./treatment/logout.php">Mon Annuaire</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<div class="container mt-5">
    <h1>Formulaire d'Inscription</h1>

    <!-- if error message display -->
    <div class="alert alert-danger" role="alert" style="min-height:50px; visibility: hidden;"> 
        <?php if(isset($errorMessage) && !empty($errorMessage)){ ?>
            <p id="<?php echo $errorMessage;?>" style="margin-bottom: 0;"><?php echo $errorMessage;?> </p> 
        <?php $errorMessage = ""; } ?>
    </div> 
        
    <form action="./treatment/user_add.php" method="POST" enctype=multipart/form-data>
        
         <!-- Champ : Prénom -->
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required value="<?php echo $prenom; ?>">
        </div>

        <!-- Champ : Nom -->
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo $nom; ?>">
        </div>

        <!-- Champ : Adresse e-mail -->
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo $email; ?>">
        </div>

        <!-- Champ : Mot de passe -->
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required value="<?php echo $password; ?>">
        </div>

        <!-- Champ : Répéter le mot de passe -->
        <div class="form-group">
            <label for="password_repeat">Répéter le mot de passe</label>
            <input type="password" class="form-control" id="password_repeat" name="password_repeat" required value="<?php echo $passwordRepeat; ?>">
        </div>

        <!-- token hidden in form -->
        <input type="hidden" name="token" id="token" value="<?php echo $token; ?>">   

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>
</html>
