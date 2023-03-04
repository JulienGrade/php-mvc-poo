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

    /**
     * Permet d'afficher la page profil à l'utilisateur connecté
     * @return void
     */
    public function profil(): void
    {
        $datas = $this->utilisateurManager->getUserInformation($_SESSION['profil']['login']);
        $_SESSION['profil']["role"] = $datas['role'];

        $data_page = [
            "page_description" => "Page de profil",
            "page_title" => "Page de profil",
            "utilisateur" => $datas,
            "view" => "views/Utilisateur/profil.view.php",
            "template" => "views/partials/template.php"
        ];
        $this->genererPage($data_page);
    }

    /**
     * Permet de se déconnecter de l'application
     * @return void
     */
    public function deconnexion(): void
    {
        Toolbox::ajouterMessageAlerte("La deconnexion est effectuée",Toolbox::COULEUR_VERTE);
        unset($_SESSION['profil']);
        header("Location: ".URL."accueil");
    }

    /**
     * Permet de vérifier la création de compte et la valider en traitant les infos comme le mot de passe
     * @throws Exception
     */
    public function validation_creerCompte($login, $password, $mail): void
    {
        if($this->utilisateurManager->verifLoginDisponible($login)){
            $passwordCrypte = password_hash($password,PASSWORD_DEFAULT);
            $clef = random_int(0,9999);
            if($this->utilisateurManager->bdCreerCompte($login,$passwordCrypte,$mail,$clef)){
                Toolbox::ajouterMessageAlerte("La compte a été créé, Un mail de validation vous a été envoyé !", Toolbox::COULEUR_VERTE);
                header("Location: ".URL."login");
            } else {
                Toolbox::ajouterMessageAlerte("Erreur lors de la création du compte, recommencez !", Toolbox::COULEUR_ROUGE);
                header("Location: ".URL."creerCompte");
            }
        } else {
            Toolbox::ajouterMessageAlerte("Le login est déjà utilisé !", Toolbox::COULEUR_ROUGE);
            header("Location: ".URL."creerCompte");
        }
    }

    // Ici on fait en sorte que la fonction fasse référence à la fonction du parent
    public function pageErreur($msg): void
    {
        parent::pageErreur($msg);
    }
}

