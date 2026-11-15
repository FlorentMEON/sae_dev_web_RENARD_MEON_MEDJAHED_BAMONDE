<?php

declare(strict_types=1);

namespace touiteur\action;

use touiteur\repository\Repository;
use touiteur\render\TouiteListRenderer;
use touiteur\message\TouiteList;

class ShowTagAction extends Action
{
    public function execute(): string
    {
        session_start();

        // On récupère l'instance du repository
        Repository::setConfig(__DIR__ . "/../../../config.ini");
        $repository = Repository::getInstance();

        // tableau des pseudo suivi par l'utilisateur
        $pseudosSuivis = $_SESSION['utisSuivis'] ?? [];

        if (isset($_SESSION['username'])){
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // Abonnement à un utilisateur

                $showTag = $_GET['tag'];

                $touites = $repository->getTouiteTag("#".$showTag);

                // On récupère la liste des touites

                $tl = new TouiteList();

                foreach ($touites as $t) {
                    $tl->ajoutTouite($t);
                }

                // On crée le renderer pour afficher la liste
                $renderer = new TouiteListRenderer($tl);

                // On construit le HTML de la page d'accueil
                $html = '<h2>Bienvenue '. $_SESSION['username'] .' !</h2>';

                $html .= '<li><a href="?action=accueil">Accueil</a></li>';
                $html .= '<li><a href="?action=my_page">Ma page</a></li>';

                if (in_array($_SESSION['username'], Action::$adminTab)){
                    $html .= '<li><a href="?action=show_influenceurs">Liste des Influenceurs</a></li>';
                }

                $html .= '<li><a href="?action=add_touite">Écrire un touite</a></li>';
                $html .= '<li><a href="?action=disconnect">Se déconnecter</a></li>';

                $html .= '<p>Touites associés au tag #'. $showTag .'</p>';

                $html .= '<form method="POST" action="?action=add_tag">';
                $html .= '<button type="submit" name="tag" value="' . $showTag . '">';
                $html .= "Suivre le tag #".$showTag;
                $html .= '</button>';
                $html .= '</form></br>';

                // On affiche la liste des touites
                $html .= $renderer->render();

            } else {
                header('Location: ?action=default');
                exit;
            }
        }

        return $html;
    }
}
