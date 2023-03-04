<?php
session_start();

define("URL", str_replace("index.php","",(isset($_SERVER['HTTPS'])? "https" : "http").
    "://".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]));
require_once("./Controllers/Securite.class.php");
require_once("./Controllers/Visiteur/VisiteurController.php");
require_once("./Controllers/Utilisateur/UtilisateurController.php");

$visiteurController = new VisiteurController();
$utilisateurController = new UtilisateurController();

try {
    if(empty($_GET['page'])){
        $page = "accueil";
    } else {
        $url = explode("/", filter_var($_GET['page'],FILTER_SANITIZE_URL));
        $page = $url[0];
    }

    switch($page){
        case "accueil" : $visiteurController->accueil();
            break;
        case "login" : $visiteurController->login();
            break;
        case "validation_login" :
            if(!empty($_POST['login']) && !empty($_POST['password'])){
                $login = Securite::secureHTML($_POST['login']);
                $password = Securite::secureHTML($_POST['password']);
                $utilisateurController->validation_login($login, $password);
            }else{
                Toolbox::ajouterMessageAlerte(
                    "Login ou mot de passe non renseigné",
                    Toolbox::COULEUR_ROUGE
                );
                header('Location:'.URL."login");
            }
            break;
        case "compte" :
            if(!Securite::estConnecte()){
                Toolbox::ajouterMessageAlerte("Veuillez vous connecter !", Toolbox::COULEUR_ROUGE);
                header("Location: ".URL."login");
            } else {
                switch($url[1]){
                    case "profil" : $utilisateurController->profil();
                        break;
                    case "deconnexion" : $utilisateurController->deconnexion();
                        break;
                    default : throw new Exception("La page n'existe pas");
                }
            }
            break;
        default : throw new RuntimeException("La page n'existe pas");
    }
} catch (Exception $e){
    $visiteurController->pageErreur($e->getMessage());
}

