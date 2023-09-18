<?php
    session_start();

    //declaration of variables
    $token = "";
    $passwordToken = "";
    $errorMessage = "";
    
    $token = uniqid(rand(), true); //token creation 
    $_SESSION['token'] = password_hash($token, PASSWORD_DEFAULT); //store in session
    $_SESSION['token_time'] = time(); //store token timestamp in session

    //get the values to set in form
    if(isset($_GET["token"]) && !empty($_GET["token"])){
        $passwordToken = $_GET["token"];
        $_SESSION["password_token"] = $passwordToken;
    }

    //get the values for display messages
    if(isset($_SESSION["message"]) && !empty($_SESSION["message"])){
        if(isset($_SESSION["message"]["error"])){$errorMessage = $_SESSION["message"]["error"];}
        unset($_SESSION["message"]);
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
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="./treatment/logout.php">Mon Site</a>
</nav>

<div class="container mt-5">
    <h1>Récupération password</h1>

    <!-- if error message display -->
    <?php if(isset($errorMessage) && !empty($errorMessage)){ ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errorMessage;?>
        </div> 
    <?php $errorMessage = ""; } ?>

    <form action="./treatment/password_set_new.php" method="POST" enctype=multipart/form-data>

            <!-- Champ : Mot de passe -->
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <!-- Champ : Répéter le mot de passe -->
        <div class="form-group">
            <label for="password_repeat">Répéter le mot de passe</label>
            <input type="password" class="form-control" id="password_repeat" name="password_repeat" required>
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
