<?php
require_once("controllers/Toolbox.class.php");

abstract class MainController{

    protected function genererPage($data): void
    {
        extract($data, EXTR_OVERWRITE);
        ob_start();
        require_once($view);
        $page_content = ob_get_clean();
        require_once($template);
    }

    protected function pageErreur($msg): void
    {
        $data_page = [
            "page_description" => "Page permettant de gérer les erreurs",
            "page_title" => "Page d'erreur",
            "msg" => $msg,
            "view" => "./views/erreur.view.php",
            "template" => "views/partials/template.php"
        ];
        $this->genererPage($data_page);
    }
}