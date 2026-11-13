<?php

declare(strict_types=1);

namespace touiteur\action;

use touiteur\repository\Repository;

class ShowInfluenceursAction extends Action
{
    public function execute(): string
    {
        session_start();

        // On récupère l'instance du repository
        Repository::setConfig(__DIR__ . "/../../../config.ini");
        $repository = Repository::getInstance();

        if (isset($_SESSION['username'])){
            
            if (in_array($_SESSION['username'], Action::$adminTab)){

                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    // On récupère la liste des influenceurs
                    $influenceurs = $repository->getInfluenceurs();

                    // On construit le HTML de la page d'accueil
                    $html = '<h2>Bienvenue '. $_SESSION['username'] .' !</h2>';

                    $html .= '<li><a href="?action=accueil">Accueil</a></li>';
                    $html .= '<li><a href="?action=my_page">Ma page</a></li>';

                    if (in_array($_SESSION['username'], Action::$adminTab)){
                        $html .= '<li><a href="?action=show_influenceurs">Liste des Influenceurs</a></li>';
                    }

                    $html .= '<li><a href="?action=add_touite">Écrire un touite</a></li>';
                    $html .= '<li><a href="?action=disconnect">Se déconnecter</a></li></br>';

                    foreach($influenceurs as $nom => $suiveurs){
                        $html .= '<p><a href="?action=show_user&showUser='. $nom .'">'. $nom .'</a> : '. $suiveurs .' suiveurs</p>';
                    }

                } else {
                    header('Location: ?action=default');
                    exit;
                }
            } else {
                header('Location: ?action=default');
                exit;
            }
        }

        return $html;
    }
}
