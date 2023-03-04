<?php

class Securite
{
    public static function secureHTML($chaine): string
    {
        // Permet de sécuriser la chaine de caractère
        return htmlentities($chaine);
    }
}

