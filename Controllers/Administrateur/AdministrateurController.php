<?php
require_once("./models/Administrateur/Administrateur.model.php");

class AdministrateurController extends MainController{
    private $administrateurManager;

    public function __construct(){
        $this->administrateurManager = new AdministrateurManager();
    }

    /**
     * @return void
     */
    public function droits(): void
    {
        $utilisateurs = $this->administrateurManager->getUtilisateurs();

        $data_page = [
            "page_description" => "Gestion des droits",
            "page_title" => "Gestion des droits",
            "utilisateurs" => $utilisateurs,
            "view" => "views/Administrateur/droits.view.php",
            "template" => "views/partials/template.php"
        ];
        $this->genererPage($data_page);
    }

    public function pageErreur($msg):void
    {
        parent::pageErreur($msg);
    }
}

