<?php

declare(strict_types=1);

namespace touiteur\action;

use touiteur\repository\Repository;
use touiteur\render\TouiteListRenderer;
use touiteur\message\TouiteList;

class DefaultAction extends Action
{
    public function execute(): string
    {
        session_start();

        // On récupère l'instance du repository
        Repository::setConfig(__DIR__ . "/../../../config.ini");
        $repository = Repository::getInstance();

        // tableau des pseudo suivi par l'utilisateur
        $pseudosSuivis = $_SESSION['utisSuivis'] ?? [];

        // tableau des tags auquels l'utilisateur est abonné
        $tagsAbonnes = $_SESSION['tagsSuivis'] ?? [];

        if (isset($_SESSION['username'])){
            $act = $_GET['action'] ?? "";

            if ($act === "my_page"){
                $touites = $repository->getTouitesUtiTag($pseudosSuivis, $tagsAbonnes);
            } else {
                $touites = $repository->getTouites();
            }

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

            // Formulaire pour s'abonner à un tag
            $html .= '<h2>S\'abonner à un tag</h2>';
            $html .= '<form method="POST" action="?action=add_tag">';
            $html .= '<label for="tag">Tag (sans #) :</label>';
            $html .= '<input type="text" id="tag" name="tag" required>';
            $html .= '<button type="submit" name="valider" value="1">S\'abonner</button>';
            $html .= '</form>';

            // On affiche les tags enregistrés
            if (count($tagsAbonnes) > 0) {
                $html .= '<p>Tags suivis : ';

                foreach ($tagsAbonnes as $tag) {
                    $tagAffiche = $tag;
                    $html .= $tagAffiche . ' ';
                }

                $html .= '</p>';
            }

            // On affiche les utilisateurs suivis
            if (count($pseudosSuivis) > 0) {
                $html .= '<p>Utilisateurs suivis : ';

                foreach ($pseudosSuivis as $pseudo) {
                    $pseudoAffiche = $pseudo;
                    $html .= $pseudoAffiche . ' ';
                }

                $html .= '</p>';
            }

            // On affiche la liste des touites
            $html .= $renderer->render();

        } else {
            $html = "<p>Veuillez vous connecter</p><br><a href=\"?action=connect\">Se connecter</a>";
        }

        return $html;
    }
}
