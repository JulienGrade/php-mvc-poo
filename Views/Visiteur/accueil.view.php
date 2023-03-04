<h1>Accueil</h1>
<?php foreach($utilisateurs as $utilisateur) :
    echo $utilisateur['login']. " - ".$utilisateur['mail'];
 endforeach; ?>
