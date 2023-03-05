<?php

require_once ('./Models/MainManager.model.php');
class UtilisateurManager extends MainManager
{
    /**
     * Permet de récupérer le mot de passe encodé en base de données d'un utilisateur
     * @param $login
     * @return mixed
     */
    private function getPasswordUser($login)
    {
        $req = "SELECT password FROM utilisateur WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login", $login, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        // Permet de clôturer la requête
        $stmt->closeCursor();
        return $res['password'];
    }

    /**
     * Permet de vérifier si la combinaison login mot de passe est valide ou non
     * @param $login
     * @param $password
     * @return bool
     */
    public function isCombinaisonValide($login, $password): bool
    {
        $passwordBD = $this->getPasswordUser($login);
        return password_verify($password, $passwordBD);
    }

    /**
     * Permet de vérifier si un comppte a été activé
     * @return false
     */
    public function compteEstActive($login): bool
    {
        $req = "SELECT est_valide FROM utilisateur WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login", $login, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        // Permet de clôturer la requête
        $stmt->closeCursor();
        return (int)$res['est_valide'];
    }

    /**
     * Permet de récupérer les informations d'un utilisateur
     * @param $login
     * @return mixed
     */
    public function getUserInformation($login)
    {
        $req = "SELECT * FROM utilisateur WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $res;
    }

    /**
     * Permet d'enregistrer la création du compte dans la base de données
     * @param $login
     * @param $passwordCrypte
     * @param $mail
     * @param $clef
     * @param $image
     * @param $role
     * @return bool
     */
    public function bdCreerCompte($login,$passwordCrypte,$mail,$clef, $image, $role): bool
    {
        $req = "INSERT INTO utilisateur (login, password, mail, est_valide, role, clef, image)
        VALUES (:login, :password, :mail, 0, :role, :clef, :image)";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->bindValue(":password",$passwordCrypte,PDO::PARAM_STR);
        $stmt->bindValue(":mail",$mail,PDO::PARAM_STR);
        $stmt->bindValue(":clef",$clef,PDO::PARAM_INT);
        $stmt->bindValue(":image",$image,PDO::PARAM_STR);
        $stmt->bindValue(":role",$role,PDO::PARAM_STR);
        $stmt->execute();
        $estModifier = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $estModifier;
    }

    /**
     * Permet de vérifier si le login existe déjà en base de données
     * @param $login
     * @return bool
     */
    public function verifLoginDisponible($login): bool
    {
        $utilisateur = $this->getUserInformation($login);
        return empty($utilisateur);
    }

    /**
     * Permet de modifier le champ est_valide après l'activation du compte par mail
     * @param $login
     * @param $clef
     * @return bool
     */
    public function bdValidationMailCompte($login,$clef): bool
    {
        $req = "UPDATE utilisateur set est_valide = 1 WHERE login = :login and clef = :clef";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->bindValue(":clef",$clef,PDO::PARAM_INT);
        $stmt->execute();
        $estModifier = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $estModifier;
    }

    /**
     * Permet d'enregistrer la modification du mail en base de données
     * @param $login
     * @param $mail
     * @return bool
     */
    public function bdModificationMailUser($login,$mail): bool
    {
        $req = "UPDATE utilisateur set mail = :mail WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->bindValue(":mail",$mail,PDO::PARAM_STR);
        $stmt->execute();
        $estModifier = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $estModifier;
    }

    /**
     * Permet d'enregistrer en base la modification de mot de passe
     * @param $login
     * @param $password
     * @return bool
     */
    public function bdModificationPassword($login,$password): bool
    {
        $req = "UPDATE utilisateur set password = :password WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->bindValue(":password",$password,PDO::PARAM_STR);
        $stmt->execute();
        $estModifier = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $estModifier;
    }

    /**
     * Permet d'enregistrer la suppression de compte
     * @param $login
     * @return bool
     */
    public function bdSuppressionCompte($login): bool
    {
        $req="DELETE FROM utilisateur WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->execute();
        $estModifier = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $estModifier;
    }

    /**
     * Permet d'enregistrer une image en base de données
     * @param $login
     * @param $image
     * @return bool
     */
    public function bdAjoutImage($login,$image): bool
    {
        $req = "UPDATE utilisateur set image = :image WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->bindValue(":image",$image,PDO::PARAM_STR);
        $stmt->execute();
        $estModifier = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $estModifier;
    }

    /**
     * Permet de récupérer l'image d'un utilisateur
     * @param $login
     * @return mixed
     */
    public function getImageUtilisateur($login){
        $req = "SELECT image FROM utilisateur WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $res['image'];
    }
}

