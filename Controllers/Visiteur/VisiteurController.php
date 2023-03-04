<?php
require_once('./Controllers/MainController.controller.php');
require_once('./Models/Visiteur/Visiteur.model.php');
class VisiteurController extends MainController
{

    private $visiteurManager;

    public function __construct()
    {
        $this->visiteurManager = new VisiteurManager();
    }

    //Propriété "page_css" : tableau permettant d'ajouter des fichiers CSS spécifiques
    //Propriété "page_javascript" : tableau permettant d'ajouter des fichiers JavaScript spécifiques
    public function accueil(): void
    {
        // Toolbox::ajouterMessageAlerte("test", Toolbox::COULEUR_VERTE);
        $utilisateurs = $this->visiteurManager->getUtilisateurs();
        $data_page = [
            "page_description" => "Description de la page d'accueil",
            "page_title" => "Titre de la page d'accueil",
            "utilisateurs" => $utilisateurs,
            "view" => "views/Visiteur/accueil.view.php",
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