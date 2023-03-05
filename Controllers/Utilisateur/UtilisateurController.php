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
                $msg = "Le compte ".$login. " n'a pas été activé par mail. ";
                $msg .= "<a href='renvoyerMailValidation/".$login."'>Renvoyez le mail de validation</a>";
                Toolbox::ajouterMessageAlerte(
                    $msg,
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
            'page_javascript' => ['profil.js'],
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
            if($this->utilisateurManager->bdCreerCompte($login,$passwordCrypte,$mail,$clef, "assets/images/profils/profil.png", "utilisateur")){
                $this->sendValidationMail($login, $mail, $clef);
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

    /**
     * Permet d'envoyer le mail de validation de création de compte
     * @param $login
     * @param $mail
     * @param $clef
     * @return void
     */
    private function sendValidationMail($login, $mail, $clef): void
    {
        // On génère une url pour valider le mail
        $urlVerification = URL."validationMail/".$login."/".$clef;
        $sujet = "Création de compte sur le site xxx";
        $message = "Pour valider votre compte veuillez cliquer sur le lien suivant : ".$urlVerification;
        Toolbox::sendMail($mail, $sujet, $message);
    }

    /**
     * Permet de renvoyer le mail de validation de création de compte
     * @param $login
     * @return void
     */
    public function renvoyerMailValidation($login): void
    {
        $utilisateur = $this->utilisateurManager->getUserInformation($login);
        $this->sendValidationMail($login,$utilisateur['mail'],$utilisateur['clef']);
        header("Location: ".URL."login");
    }

    /**
     * Permet d'enregistrer l'activation du compte, sa validation par mail
     * @param $login
     * @param $clef
     * @return void
     */
    public function validation_mailCompte($login,$clef): void
    {
        if($this->utilisateurManager->bdValidationMailCompte($login,$clef)){
            Toolbox::ajouterMessageAlerte("Le compte a été activé !", Toolbox::COULEUR_VERTE);
            $_SESSION['profil'] = [
                "login" => $login,
            ];
            header('Location: '.URL.'compte/profil');
        } else {
            Toolbox::ajouterMessageAlerte("Le compte n'a pas été activé !", Toolbox::COULEUR_ROUGE);
            header('Location: '.URL.'creerCompte');
        }
    }

    /**
     * Permet de traiter la modification de l'adresse mail
     * @param $mail
     * @return void
     */
    public function validation_modificationMail($mail): void
    {
        if($this->utilisateurManager->bdModificationMailUser($_SESSION['profil']['login'],$mail)){
            Toolbox::ajouterMessageAlerte("La modification est effectuée", Toolbox::COULEUR_VERTE);
        } else {
            Toolbox::ajouterMessageAlerte("Aucune modification effectuée", Toolbox::COULEUR_ROUGE);
        }
        header("Location: ".URL."compte/profil");
    }

    /**
     * Permet d'afficher la page de modification de mot de passe
     * @return void
     */
    public function modificationPassword(): void
    {
        $data_page = [
            "page_description" => "Page de modification du password",
            "page_title" => "Page de modification du password",
            "page_javascript" => ["modificationPassword.js"],
            "view" => "views/Utilisateur/modificationPassword.view.php",
            "template" => "views/partials/template.php"
        ];
        $this->genererPage($data_page);
    }

    /**
     * Permet de valider et d'enregistrer la modification de mot de passe
     * @param $ancienPassword
     * @param $nouveauPassword
     * @param $confirmationNouveauPassword
     * @return void
     */
    public function validation_modificationPassword($ancienPassword,$nouveauPassword,$confirmationNouveauPassword): void
    {
        if($nouveauPassword === $confirmationNouveauPassword){
            if($this->utilisateurManager->isCombinaisonValide($_SESSION['profil']['login'],$ancienPassword)){
                $passwordCrypte = password_hash($nouveauPassword,PASSWORD_DEFAULT);
                if($this->utilisateurManager->bdModificationPassword($_SESSION['profil']['login'],$passwordCrypte)){
                    Toolbox::ajouterMessageAlerte("La modification du password a été effectuée", Toolbox::COULEUR_VERTE);
                    header("Location: ".URL."compte/profil");
                } else {
                    Toolbox::ajouterMessageAlerte("La modification a échouée", Toolbox::COULEUR_ROUGE);
                    header("Location: ".URL."compte/modificationPassword");
                }
            } else {
                Toolbox::ajouterMessageAlerte("La combinaison login / ancien password ne correspond pas", Toolbox::COULEUR_ROUGE);
                header("Location: ".URL."compte/modificationPassword");
            }
        } else {
            Toolbox::ajouterMessageAlerte("Les passwords ne correspondent pas", Toolbox::COULEUR_ROUGE);
            header("Location: ".URL."compte/modificationPassword");
        }
    }

    /**
     * Permet de supprimer son compte
     * @return void
     */
    public function suppressionCompte(): void
    {
        $this->dossierSuppressionImageUtilisateur($_SESSION['profil']['login']);
        rmdir("public/assets/images/profils/".$_SESSION['profil']['login']);
        if($this->utilisateurManager->bdSuppressionCompte($_SESSION['profil']['login'])) {
            Toolbox::ajouterMessageAlerte("La suppression du compte est effectuée", Toolbox::COULEUR_VERTE);
            $this->deconnexion();
        } else {
            Toolbox::ajouterMessageAlerte("La suppression n'a pas été effectuée. Contactez l'administrateur",Toolbox::COULEUR_ROUGE);
            header("Location: ".URL."compte/profil");
        }
    }

    /**
     * Permet de valider et enregistrer une image
     * @param $file
     * @return void
     */
    public function validation_modificationImage($file): void
    {
        try{
            $repertoire = "public/assets/images/profils/".$_SESSION['profil']['login']."/";
            $nomImage = Toolbox::ajoutImage($file,$repertoire);//ajout image dans le répertoire
            //Supression de l'ancienne image
            $this->dossierSuppressionImageUtilisateur($_SESSION['profil']['login']);
            //Ajout de la nouvelle image dans la BD
            $nomImageBD = "profils/".$_SESSION['profil']['login']."/".$nomImage;
            if($this->utilisateurManager->bdAjoutImage($_SESSION['profil']['login'],$nomImageBD)){
                Toolbox::ajouterMessageAlerte("La modification de l'image est effectuée", Toolbox::COULEUR_VERTE);
            } else {
                Toolbox::ajouterMessageAlerte("La modification de l'image n'a pas été effectuée", Toolbox::COULEUR_ROUGE);
            }
        } catch(Exception $e){
            Toolbox::ajouterMessageAlerte($e->getMessage(), Toolbox::COULEUR_ROUGE);
        }

        header("Location: ".URL."compte/profil");
    }

    /**
     * Permet de supprimer une image
     * @param $login
     * @return void
     */
    private function dossierSuppressionImageUtilisateur($login): void
    {
        $ancienneImage = $this->utilisateurManager->getImageUtilisateur($_SESSION['profil']['login']);
        if($ancienneImage !== "profils/profil.png"){
            unlink("public/Assets/images/".$ancienneImage);
        }
    }

    // Ici on fait en sorte que la fonction fasse référence à la fonction du parent
    public function pageErreur($msg): void
    {
        parent::pageErreur($msg);
    }
}

