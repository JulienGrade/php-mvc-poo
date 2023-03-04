<?php

require_once ('./Models/MainManager.model.php');
class UtilisateurManager extends MainManager
{
    public function getUtilisateurs(){
        $req = $this->getBdd()->prepare("SELECT * FROM utilisateur");
        $req->execute();
        $datas = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();
        return $datas;
    }

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
}

