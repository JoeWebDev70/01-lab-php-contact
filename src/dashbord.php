<?php
    //TODO : voir pour mettre token remember ds autre table et set un time
    session_start();

    //declaration of variables
    $result = [""];
    $idContact = "";
    $prenom = "";
    $nom = "";
    $email = "";
    $url = "";
    $txtBtn = "";
    $txtBtnCancel = "";
    $token = "";
    $token_time = "";
    $userName = "";
    $successMessage = "";
    $errorMessage = "";
    $lifeTimeSession = 1*60;
    $rememberMe = "";

    if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
        //check because if remember me was set before not need to check lifetime
        if(isset($_SESSION['last_access']) && !empty($_SESSION['last_access'])){
            // Check if user interact with page or not then : log out or set new time for last_access
            if(time()- intval($_SESSION['last_access']) > $lifeTimeSession){
                header('location: ./treatment/logout.php');
            }else{
                $_SESSION['last_access'] = time();
            }
        }

        //get user name to display it on dashbord
        if(isset($_SESSION["user"]["name"]) && !empty($_SESSION["user"]["name"])){
            $userName = $_SESSION["user"]["name"];
        }

        //get contact list only if user session is set
        if(isset($_SESSION["contacts"]) && !empty($_SESSION["contacts"])){
            $result = $_SESSION["contacts"];
        }
    }

    $token = uniqid(rand(), true); //token creation 
    $_SESSION['token'] = password_hash($token, PASSWORD_DEFAULT); //store in session
    $_SESSION['token_time'] = time(); //store token timestamp in session

    //get contact for form
    if(isset($_SESSION["contact"]) && !empty($_SESSION["contact"])){
        if(isset($_SESSION["contact"]["id"])){$idContact = $_SESSION["contact"]["id"];}
        if(isset($_SESSION["contact"]["name"])){$prenom = $_SESSION["contact"]["name"];}
        if(isset($_SESSION["contact"]["surname"])){$nom = $_SESSION["contact"]["surname"];}
        if(isset($_SESSION["contact"]["email"])){$email = $_SESSION["contact"]["email"];}
        unset($_SESSION["contact"]);
    }

     // get the values for display messages
    if(isset($_SESSION["message"]) && !empty($_SESSION["message"])){
        if(isset($_SESSION["message"]["error"])){$errorMessage = $_SESSION["message"]["error"];}
        if(isset($_SESSION["message"]["success"])){$successMessage = $_SESSION["message"]["success"];}
        unset($_SESSION["message"]);
    }

    //if remember was set in this connection of user then process it: set the token in hidden input
    if(isset($_SESSION['remember_me']) && !empty($_SESSION['remember_me'])){
        $rememberMe = $_SESSION['remember_me'];
        unset($_SESSION['remember_me']);
    }

    if($idContact == ""){ 
        $url = "./treatment/contact_add.php" ;
        $txtBtn = "Ajouter Contact";
        $txtBtnCancel = "";
    }else {
        $url = "./treatment/contact_modify.php";
        $txtBtn = "Valider";
        $txtBtnCancel = "Annuler";
    }

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Contacts</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="./treatment/logout.js" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <h1 class="navbar-brand" style="font-weight:400">Mon Tableau de Bord</h1>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">            
            <li class="nav-item">
                <a class="nav-link" id="logout" href="./treatment/logout.php">Se déconnecter</a>
            </li>
        </ul>
    </div>
</nav>

<!-- remember treatment for further connections -->
<!-- token hidden -->
<input type="hidden" name="rememberMeToken" id="rememberMeToken" value="<?php echo $rememberMe; ?>">
<script>
    // get the token set with php and store it in localstorage
    const rememberMe = document.querySelector("#rememberMeToken").value;
    if(rememberMe != ""){
        localStorage.setItem("rememberMe", JSON.stringify(rememberMe));
        rememberMe.value = "";
    }
</script>
<!-- remember treatment for further connections -->

<div class="container mt-5">
    <h2>Tableau de Bord - Gestion des Contacts</h2>
    <p>Bienvenue <strong> <?php echo $userName; ?> </strong> dans votre tableau de bord de gestion des contacts. Vous pouvez ajouter, modifier ou supprimer des contacts ici.</p>

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

    <div class="row">
        <div class="col-md-4">
            <!-- Formulaire d'ajout de contact -->
            <form action="<?php echo $url; ?>" method="POST" enctype=multipart/form-data>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo $nom; ?>">
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required value="<?php echo $prenom; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo $email; ?>">
                </div>
                <!-- token hide for check validity -->
                <input type="hidden" name="token" id="token" value="<?php echo $token; ?>">  

                <button type="submit" class="btn btn-primary"><?php echo $txtBtn; ?></button>
                <?php if(isset($txtBtnCancel) && !empty($txtBtnCancel)){ ?>
                    <button type="submit" id="btnCancel" class="btn btn-primary"><?php echo $txtBtnCancel; ?></button>
                <?php }?>
            </form>
        </div>

        <!-- Liste des contacts -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between mb-2">
                <h3>Liste des Contacts</h3> 
                <button type="submit" class="btn btn-primary">Supprimer Sélection</button>
            </div>
            <form action="" method="POST" enctype=multipart/form-data>
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">Selection</th>
                        <th scope="col">Prénom</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Email</th>
                        <th scope="col">Supprimer</th>
                        <th scope="col">Modifier</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result as $row){ ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="selected[]" value="<?=$row['id']; ?>">
                            </td>
                            <td>
                                <?=$row['name']; ?>
                            </td>
                            <td>
                                <?=$row['surname']; ?>
                            </td>
                            <td>
                                <?=$row['email']; ?>
                            </td>
                            <td class="text-center">
                                <a href=""> 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                        <path
                                        d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z" />
                                    </svg>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path
                                        d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </form>
        </div>
    </div>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
