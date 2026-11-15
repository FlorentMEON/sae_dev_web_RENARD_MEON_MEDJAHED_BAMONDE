<?php

declare(strict_types=1);

namespace touiteur\action;

use touiteur\repository\Repository;
use touiteur\render\TouiteRenderer;
use touiteur\message\TouiteList;

class ShowTouiteAction extends Action
{
    public function execute(): string
    {
        session_start();

        // On récupère l'instance du repository
        Repository::setConfig(__DIR__ . "/../../../config.ini");
        $repository = Repository::getInstance();

        if (isset($_SESSION['username'])){
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // id du touite à afficher

                $idTouite = $_GET['id_touite'];

                // On récupère le touite
                $touite = $repository->getTouiteById($idTouite);

                // On crée le renderer pour afficher le touite
                $renderer = new TouiteRenderer($touite);

                // On construit le HTML de la page d'accueil
                $html = '<h2>Bienvenue '. $_SESSION['username'] .' !</h2>';

                $html .= '<li><a href="?action=accueil">Accueil</a></li>';
                $html .= '<li><a href="?action=my_page">Ma page</a></li>';

                if (in_array($_SESSION['username'], Action::$adminTab)){
                    $html .= '<li><a href="?action=show_influenceurs">Liste des Influenceurs</a></li>';
                }

                $html .= '<li><a href="?action=add_touite">Écrire un touite</a></li>';
                $html .= '<li><a href="?action=disconnect">Se déconnecter</a></li></br>';

                // On affiche la liste des touites
                $html .= $renderer->renderDetail();

                $showUserName = $touite->utilisateur;

                $html .= '<form method="POST" action="?action=add_user">';
                $html .= '<button type="submit" name="addUser" value="' . $showUserName . '">';
                $html .= "Suivre l'utilisateur ".$showUserName;
                $html .= '</button>';
                $html .= '</form></br>';

            } else {
                header('Location: ?action=default');
                exit;
            }
        }

        return $html;
    }
}
