<?php
class Toolbox {
    public const COULEUR_ROUGE = "alert-danger";
    public const COULEUR_ORANGE = "alert-warning";
    public const COULEUR_VERTE = "alert-success";

    /**
     * Permet d'ajouter une alert
     * @param $message
     * @param $type
     * @return void
     */
    public static function ajouterMessageAlerte($message,$type): void
    {
        $_SESSION['alert'][]=[
            "message" => $message,
            "type" => $type
        ];
    }

    /**
     * Permet d'envoyer un email
     * @param $destinataire
     * @param $sujet
     * @param $message
     * @return void
     */
    public static function sendMail($destinataire, $sujet, $message): void
    {
        $headers = "From : gradejulien@gmail.com";
        if(mail($destinataire,$sujet,$message,$headers)){
            self::ajouterMessageAlerte(
                "Le mail a bien été envoyé",
                self::COULEUR_VERTE
            );
        }else{
            self::ajouterMessageAlerte(
                "Le mail n'a pas été envoyé",
                self::COULEUR_ROUGE
            );
        }
    }

    /**
     * Permet d'uploader une image
     * @throws Exception
     */
    public static function ajoutImage($file, $dir): string
    {
        // On teste que l'on récupère bien un fichier
        if(!isset($file['name']) || empty($file['name'])) {
            throw new RuntimeException("Vous devez indiquer une image");
        }
        // On teste que l'on ait bien un répertoire sinon on le créé
        if(!file_exists($dir)){
            mkdir($dir,0777);
        }
        // On récupère l'extension du fichier, son type MIME
        $extension = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        $random = random_int(0,99999);
        // On enregistre le nom de notre fichier
        $target_file = $dir. $random . "_". $file['name'];
        // On teste le type MIME que ce soit bien un type image
        if(!getimagesize($file["tmp_name"])) {
            throw new RuntimeException("Le fichier n'est pas une image");
        }
        // On teste que le type MIME corresponde à ce que l'on autorise
        if($extension !== "jpg" && $extension !== "jpeg" && $extension !== "png" && $extension !== "gif") {
            throw new RuntimeException("L'extension du fichier n'est pas reconnu");
        }
        // On teste que l'on n'a pas déjà un fichier avec ce nom
        if(file_exists($target_file)) {
            throw new RuntimeException("Le fichier existe déjà");
        }
        // On teste que le fichier ne dépasse pas un certain poids
        if($file['size'] > 500000) {
            throw new RuntimeException("Le fichier est trop gros");
        }
        // On teste que l'enregistrement du fichier dans le répertoire souhaité se soit fait
        if(!move_uploaded_file($file['tmp_name'], $target_file)) {
            throw new RuntimeException("l'ajout de l'image n'a pas fonctionné");
        }else {
            return ($random . "_" . $file['name']);
        }
    }
}