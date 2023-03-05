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
     * @return bool
     */
    public function bdCreerCompte($login,$passwordCrypte,$mail,$clef): bool
    {
        $req = "INSERT INTO utilisateur (login, password, mail, est_valide, role, clef, image)
        VALUES (:login, :password, :mail, 0, 'utilisateur', :clef, '')";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->bindValue(":password",$passwordCrypte,PDO::PARAM_STR);
        $stmt->bindValue(":mail",$mail,PDO::PARAM_STR);
        $stmt->bindValue(":clef",$clef,PDO::PARAM_INT);
        $stmt->execute();
        $estModifier = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $estModifier;
    }

    /**
     * Permet de vérifier si la lgogin existe déjà en base de données
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
}

