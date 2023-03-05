<?php

class Securite
{
    public const COOKIE_NAME="timers";

    /**
     * Permet de sécuriser une chaine de caractère en enlevant les caractères spéciaux
     * @param $chaine
     * @return string
     */
    public static function secureHTML($chaine): string
    {
        // Permet de sécuriser la chaine de caractère
        return htmlentities($chaine);
    }

    /**
     * Permet de vérifier si on a un utilisateur connecté
     * @return bool
     */
    public static function estConnecte(): bool
    {
        return (!empty($_SESSION['profil']));
    }

    /**
     * Vérifie si l'utilisateur a le role utilisateur
     * @return bool
     */
    public static function estUtilisateur(): bool
    {
        return ($_SESSION['profil']['role'] === "utilisateur");
    }

    /**
     * Vérifie si l'utilisateur a le role administrateur
     * @return bool
     */
    public static function estAdministrateur(): bool
    {
        return ($_SESSION['profil']['role'] === "administrateur");
    }

    /**
     * Permet d'enregistrer un code unique dans un cookie créé et dans la session
     * @return void
     * @throws Exception
     */
    public static function genererCookieConnexion():void
    {
        // on crée une clé unique complexe
        $ticket = session_id().microtime().random_int(0,999999);
        $ticket = hash("sha512",$ticket);
        // on l'enregistre comme cookie durée 20 minutes
        setcookie(self::COOKIE_NAME,$ticket,time()+(60*20));
        // on l'enregistre dans la session
        $_SESSION['profil'][self::COOKIE_NAME] = $ticket;
    }

    /**
     * Permet de comparer le cookie timers avec la valeur en session
     * @return bool
     */
    public static function checkCookieConnexion(): bool
    {
        return $_COOKIE[self::COOKIE_NAME] === $_SESSION['profil'][self::COOKIE_NAME];
    }
}

