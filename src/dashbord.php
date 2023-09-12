<?php

    session_start();
    
    //declarations of variables
    $lifeTimeSession = 1*60;

    // Check if user interact with page or not then : log out or set new time for last_access
    if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
        if(time()- intval($_SESSION['last_access']) > $lifeTimeSession){
            header('location: ./treatment/logout.php');
        }else{
            $_SESSION['last_access'] = time();
        }
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
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <h1 class="navbar-brand" style="font-weight:400">Mon Tableau de Bord</h1>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">            
            <li class="nav-item">
                <a class="nav-link" href="./treatment/logout.php">Se déconnecter</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Tableau de Bord - Gestion des Contacts</h2>
    <p>Bienvenue dans votre tableau de bord de gestion des contacts. Vous pouvez ajouter, modifier ou supprimer des contacts ici.</p>
    <div class="row">
        <div class="col-md-6">
            <!-- Formulaire d'ajout de contact -->
            <form>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter Contact</button>
            </form>
        </div>
        <div class="col-md-6">
            <!-- Liste des contacts -->
            <h3>Liste des Contacts</h3>
            <ul class="list-group">
                <li class="list-group-item">John Doe - john.doe@example.com</li>
                <li class="list-group-item">Jane Smith - jane.smith@example.com</li>
                <li class="list-group-item">Michael Johnson - michael.johnson@example.com</li>
            </ul>
        </div>
    </div>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
