<?php
require_once('./Controllers/MainController.controller.php');
require_once ('./Models/Utilisateur/Utilisateur.model.php');
class UtilisateurController extends MainController
{
    private $utilisateurManager;

    public function __construct()
    {
        $this->utilisateurManager = new UtilisateurManager();
    }

    //Propriété "page_css" : tableau permettant d'ajouter des fichiers CSS spécifiques
    //Propriété "page_javascript" : tableau permettant d'ajouter des fichiers JavaScript spécifiques
    /**
     * Permet de traiter la demande de connexion
     * @param $login
     * @param $password
     * @return void
     */
    public function validation_login($login, $password): void
    {
        if($this->utilisateurManager->isCombinaisonValide($login, $password)){
            if($this->utilisateurManager->compteEstActive($login)){
                Toolbox::ajouterMessageAlerte(
                    "Bon retour parmi nous !",
                    Toolbox::COULEUR_VERTE
                );
                $_SESSION['profil'] = [
                    "login" => $login
                ];
                header('Location:'.URL."compte/profil");
            }else{
                Toolbox::ajouterMessageAlerte(
                    "Le compte n'a pas été activé par mail !",
                    Toolbox::COULEUR_ROUGE
                );
                header('Location:'.URL."login");
            }
        }else{
            Toolbox::ajouterMessageAlerte(
                "Combinaison login/mot de passe non valide !",
                Toolbox::COULEUR_ROUGE
            );
            header('Location:'.URL."login");
        }
    }

    // Ici on fait en sorte que la fonction fasse référence à la fonction du parent
    public function pageErreur($msg): void
    {
        parent::pageErreur($msg);
    }
}
