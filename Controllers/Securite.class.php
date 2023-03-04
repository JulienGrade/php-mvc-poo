<?php

class Securite
{
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
}

