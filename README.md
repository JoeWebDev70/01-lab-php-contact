# 01-lab-php-contact

CONSIGNE
Votre défi consiste à intégrer cette application Web et de réaliser la partie back d’une pe􀆟te application qui permet ajouter des contacts. A partir des fichiers fournis réaliser les code php.

Organiser une base de données :
    MCD avec workbench pour une application qui permet à des utilisateur de s’inscrire sur le site. 
    Il faut stocker leur nom, prénom, email qui servira d’identifiant et le mot de passe. 
    Cette application permettra aussi aux utilisateur de stocker des contacts avec le nom, prénom et adresse mail du contact.
    Attention quand un utilisateur se connectera au site il ne verra que ses contact à lui.
    Mettre en œuvre la base de données à partir d’un fichier sql que vous importerez.

Formulaire d’inscription :
    Réaliser le code php qui permet de stocker des utilisateurs de l’application. 
    Attention sécuriser les données d’entrées et crypter les mots de passe dans la base de données 

Formulaire de login :
    Réaliser le code php qui va vérifier si l’utilisateur existe bien et si c’est la cas rediriger l’utilisateur sur la page du Dashboard.

Dashbord :
    Réaliser le code php qui permet d’ajouter, supprimer, modifier dans la base de données un contact. 
    On ne peut pas rentrer deux fois l’adresse mail d’un contact. 
    Afficher cette adresse email existe déjà. 
    Rattacher ce contact à l’utilisateur et ce nouveau contact devra apparaître dans la liste.

Serveur de base de données : Sur un container Docker
Serveur apache + php sur un autre container.
On pourra atteindre ce projet avec le nom de domaine : php-dev-1.online
Utiliser git. Ce repositories sera public.
Fichier fournie : start-01-lab-php-contact

Production attendue :
- Page PHP et autres si nécessaires
- Le diagramme du modèle de données
- L’export de ta base de données db.sql