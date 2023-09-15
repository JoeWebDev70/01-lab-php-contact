<?php

    session_start();

    //declaration of variables
    $result = [""];
    $idContact = "";
    $contactDelete = "";
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
    $delete = "";

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

        $token = uniqid(rand(), true); //token creation
        $_SESSION['token'] = password_hash($token, PASSWORD_DEFAULT); //store in session
        $_SESSION['token_time'] = time(); //store token timestamp in session

        //get contact list only if user session is set
        if(isset($_SESSION["contacts"]) && !empty($_SESSION["contacts"])){
            $result = $_SESSION["contacts"];
        }

        //get contact for form
        if(isset($_SESSION["contact"]) && !empty($_SESSION["contact"])){
            if(isset($_SESSION["contact"]["id"])){$idContact = $_SESSION["contact"]["id"];}
            if(isset($_SESSION["contact"]["name"])){$prenom = $_SESSION["contact"]["name"];}
            if(isset($_SESSION["contact"]["surname"])){$nom = $_SESSION["contact"]["surname"];}
            if(isset($_SESSION["contact"]["email"])){$email = $_SESSION["contact"]["email"];}
            unset($_SESSION["contact"]);
        }
    }


     // get the values for display messages
    if(isset($_SESSION["message"]) && !empty($_SESSION["message"])){
        if(isset($_SESSION["message"]["error"])){$errorMessage = $_SESSION["message"]["error"];}
        if(isset($_SESSION["message"]["success"])){$successMessage = $_SESSION["message"]["success"];}
        unset($_SESSION["message"]);
    }

    //if remember was set in this connection of user then process it: set the token in hidden input
    if(isset($_SESSION['remember']['token']) && !empty($_SESSION['remember']['token'])
    && isset($_SESSION['remember']['time']) && !empty($_SESSION['remember']['time'])){
        $rememberMe = json_encode(['token' => $_SESSION['remember']['token'], 'time'=> $_SESSION['remember']['time']]);
        unset($_SESSION['remember']);
    }

    if($idContact == ""){
        $url = "./treatment/contact_add.php" ;
        $txtBtn = "Ajouter Contact";
        $txtBtnCancel = "";
    }else {
        $url = "./treatment/contact_update.php";
        $txtBtn = "Valider";
        $txtBtnCancel = "Annuler";
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Contacts</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="./treatment/logout.js" defer></script>
    <script src="./treatment/contact_confirm_delete.js" defer></script>
</head>
<body>

<!-- modal for confirm delete -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirmation de suppression</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Confirmez-vous la suppression des contacts ?</p>
      </div>
      <div class="modal-footer">
            <button type="submit" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button type="submit" id="btnValidate"  class="btn btn-primary">Valider</button>
      </div>
    </div>
  </div>
</div>

<!-- modal for confirm delete -->

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
<script>
    //set in local storage
    <?php if($rememberMe == ""){ 
        echo "const rememberMe = '';";
    }else{ ?>
        const rememberMe = <?php echo $rememberMe; ?>;
    <?php } ?>
    if(rememberMe != ""){
        localStorage.setItem("rememberMe", JSON.stringify(rememberMe));
    }
</script>
<!-- remember treatment for further connections -->

<div class="container mt-5">
    <h1>Tableau de Bord - Gestion des Contacts</h1>
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
        <!-- Formulaire d'ajout de contact -->
        <div class="col-md-4">
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
                <!-- hidden idcontact for modify -->
                <input type="hidden" name="id_contact" id="id_contact" value="<?php echo $idContact; ?>">

                <!-- token hide for check validity -->
                <input type="hidden" name="token" id="token" value="<?php echo $token; ?>">

                <button type="submit" name="btnValidate" class="btn btn-primary"><?php echo $txtBtn; ?></button>
                <?php if(isset($txtBtnCancel) && !empty($txtBtnCancel)){ ?>
                    <button type="submit" name="btnCancel" class="btn btn-primary"><?php echo $txtBtnCancel; ?></button>
                <?php }?>
            </form>
        </div>

        <!-- Liste des contacts -->
        <div class="col-md-8">
            <!-- <form action="" method="POST" enctype=multipart/form-data> -->
                <div class="d-flex justify-content-between mb-2">
                    <h2>Liste des Contacts</h2>
                    <button type="submit" id="btnSelection" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">Supprimer Sélection</button>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col" class="text-center">Selection</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Prénom</th>
                        <th scope="col">Email</th>
                        <th scope="col" class="text-center">Supprimer</th>
                        <th scope="col" class="text-center">Modifier</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result as $row){ ?>
                        <tr>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="select" name="selected[]" value="<?=$row['id']; ?>">
                            </td>
                            <td class="align-middle">
                                <?=$row['surname']; ?>
                            </td>
                            <td class="align-middle">
                                <?=$row['name']; ?>
                            </td>
                            <td class="align-middle">
                                <?=$row['email']; ?>
                            </td>
                            <td class="text-center">
                                <button class="selectOne btn btn-link" value="<?=$row['id']; ?>" data-toggle="modal" data-target="#confirmModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                        <path
                                        d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z" />
                                    </svg>
                                </button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-link">
                                    <a href="./treatment/contact_modify.php?id_contact=<?=$row['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                            <path
                                            d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                                        </svg>
                                    </a>
                                </button></td>
                        </tr>
                        <?php } ?>
                    </table>
                <!-- </form> -->
        </div>
    </div>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

</body>
</html>
