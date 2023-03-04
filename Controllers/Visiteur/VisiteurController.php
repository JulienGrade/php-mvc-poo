<?php
require_once('./Controllers/MainController.controller.php');
class VisiteurController extends MainController
{

    //Propriété "page_css" : tableau permettant d'ajouter des fichiers CSS spécifiques
    //Propriété "page_javascript" : tableau permettant d'ajouter des fichiers JavaScript spécifiques
    /**
     * Permet d'afficher la page d'accueil du site
     * @return void
     */
    public function accueil(): void
    {
        // Toolbox::ajouterMessageAlerte("test", Toolbox::COULEUR_VERTE);

        $data_page = [
            "page_description" => "Description de la page d'accueil",
            "page_title" => "Accueil",
            "view" => "views/Visiteur/accueil.view.php",
            "template" => "views/partials/template.php"
        ];
        $this->genererPage($data_page);
    }

    /**
     * Permet d'afficher la page de connexion
     * @return void
     */
    public function login(): void
    {
        $data_page = [
            "page_description" => "Description de la page de connexion",
            "page_title" => "Connexion",
            "view" => "views/Visiteur/login.view.php",
            "template" => "views/partials/template.php"
        ];
        $this->genererPage($data_page);
    }

    // Ici on fait en sorte que la fonction fasse référence à la fonction du parent
    public function pageErreur($msg): void
    {
        parent::pageErreur($msg);
    }
}