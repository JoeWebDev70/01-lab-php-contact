<?php
    session_start(); //https://www.php.net/manual/fr/reserved.variables.session.php

    //declarations  of variables
    $prenom = "";
    $nom = "";
    $email = "";
    $password = "";
    $passwordRepeat = "";
    $errorMessage = "";
    $token = "";

    $token = uniqid(rand(), true); //token creation when arrive on this new page
    $_SESSION['token'] = $token; //stockage
    $_SESSION['token_time'] = time(); //stockage tokentime stamp 
    
    if(isset($_SESSION["user"]) && !empty($_SESSION["user"])){
        if(isset($_SESSION["user"]["name"])){$prenom = $_SESSION["user"]["name"];}
        if(isset($_SESSION["user"]["surname"])){$nom = $_SESSION["user"]["surname"];}
        if(isset($_SESSION["user"]["email"])){$email = $_SESSION["user"]["email"];}
        if(isset($_SESSION["user"]["password"])){$password = $_SESSION["user"]["password"];}
        if(isset($_SESSION["user"]["password_repeat"])){$passwordRepeat = $_SESSION["user"]["password_repeat"];}
        unset($_SESSION["user"]);
    }

    if(isset($_SESSION["message"]) && !empty($_SESSION["message"])){
        if(isset($_SESSION["message"]["error"])){
            $errorMessage = $_SESSION["message"]["error"];
            unset($_SESSION["message"]["error"]);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="./treatment/logout.php">Mon Site</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<div class="container mt-5">
    <h2>Formulaire d'Inscription</h2>
    <form action="./treatment/add_user.php" method="POST" enctype=multipart/form-data>
        
         <!-- if error message display -->
        <?php if(isset($errorMessage) && !empty($errorMessage)){ ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage;?>
            </div> 
        <?php $errorMessage = ""; } ?>

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

        <!-- jeton caché dans formulaire -->
        <input type="hidden" name="token" id="token" value="<?php echo $token; ?>">   

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
