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

    /**
     * Validation et enregistrement de la modification de rôle
     * @param $login
     * @param $role
     * @return void
     */
    public function validation_modificationRole($login,$role): void
    {
        if($this->administrateurManager->bdModificationRoleUser($login,$role)){
            Toolbox::ajouterMessageAlerte("La modification a été prise en compte", Toolbox::COULEUR_VERTE);
        } else {
            Toolbox::ajouterMessageAlerte("La modification n'a pas été prise en compte", Toolbox::COULEUR_ROUGE);
        }
        header("Location: ".URL."administration/droits");
    }

    public function pageErreur($msg):void
    {
        parent::pageErreur($msg);
    }
}

