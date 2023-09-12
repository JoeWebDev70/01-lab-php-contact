<?php

    session_start();
   
    //declarations of variables
    $email = "";
    $password = "";
    $token = "";
    $token_time = "";
    $successMessage = "";
    $errorMessage = "";

    $token = uniqid(rand(), true); //token creation 
    $_SESSION['token'] = $token; //stockage
    $_SESSION['token_time'] = time(); //stockage tokentime stamp 

    if(isset($_SESSION["email"]) && !empty($_SESSION["email"])){
        $email = $_SESSION["email"];
        unset($_SESSION["email"]);
    }
    
    if(isset($_SESSION["password"]) && !empty($_SESSION["password"])){
        $password = $_SESSION["password"];
        unset($_SESSION["password"]);
    }
 
    if(isset($_SESSION["message"]) && !empty($_SESSION["message"])){
        if(isset($_SESSION["message"]["error"])){$errorMessage = $_SESSION["message"]["error"];}
        if(isset($_SESSION["message"]["success"])){$successMessage = $_SESSION["message"]["success"];}
        unset($_SESSION["message"]);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <h1 class="navbar-brand" style="font-weight:400">Mon Site</h1>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">            
            <li class="nav-item">
                <a class="nav-link" href="./signup.html">S'inscrire</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Formulaire de Connexion</h2>
    <form action="./treatment/login.php" method="POST" enctype=multipart/form-data>
        
        <!-- if success message display -->
        <?php if(isset($successMessage) && !empty($successMessage)){ ?>
            <div class="alert alert-success" role="alert">
                <?php echo $successMessage;?>
            </div> 
        <?php $successMessage = ""; } ?>
        
         <!-- if error message display -->
        <?php if(isset($errorMessage) && !empty($errorMessage)){ ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage;?>
            </div> 
        <?php $errorMessage = ""; } ?>

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

        <!-- Option : Se souvenir de moi -->
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
            <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
        </div>

        <!-- Lien : Mot de passe oublié -->
        <div class="form-group">
            <a href="#">Mot de passe oublié ?</a>
        </div>

        <!-- jeton caché dans formulaire -->
        <input type="hidden" name="token" id="token" value="<?php echo $token; ?>">

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
