<?php 

class AdministrateurManager extends MainManager{
    /**
     * Récupère les utilisateurs en base de données
     * @return mixed
     */
    public function getUtilisateurs(){
        $req = $this->getBdd()->prepare("SELECT * FROM utilisateur");
        $req->execute();
        $datas = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();
        return $datas;
    }

    /**
     * Enregistre la modification du rôle en base de données
     * @param $login
     * @param $role
     * @return bool
     */
    public function bdModificationRoleUser($login,$role): bool
    {
        $req = "UPDATE utilisateur set role = :role WHERE login = :login";
        $stmt = $this->getBdd()->prepare($req);
        $stmt->bindValue(":login",$login,PDO::PARAM_STR);
        $stmt->bindValue(":role",$role,PDO::PARAM_STR);
        $stmt->execute();
        $estModifier = ($stmt->rowCount() > 0);
        $stmt->closeCursor();
        return $estModifier;
    }
}



