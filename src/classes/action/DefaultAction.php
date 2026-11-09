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
        // On récupère les tags déjà enregistrés
        $tagsAbonnes = [];

        if (isset($_COOKIE['tag_abonne']) && $_COOKIE['tag_abonne'] !== '') {
            $tagsAbonnes = explode(',', $_COOKIE['tag_abonne']);
        }

        // On vérifie que le formulaire a été envoyé en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['valider']) && isset($_POST['tag']) && $_POST['tag'] !== '') {

                $nouveauTag = $_POST['tag'];

                // On vérifie que le tag ne contient pas de virgule
                if (count(explode(',', $nouveauTag)) === 1) {

                    // On vérifie que le tag n'est pas déjà enregistré
                    $dejaAbonne = false;

                    foreach ($tagsAbonnes as $tag) {
                        if ($tag === $nouveauTag) {
                            $dejaAbonne = true;
                        }
                    }

                    // Si le tag est nouveau, on l'ajoute
                    if (!$dejaAbonne) {
                        $tagsAbonnes[] = $nouveauTag;

                        // On enregistre tous les tags dans le cookie
                        setcookie('tag_abonne', implode(',', $tagsAbonnes), time() + 60 * 60 * 24 * 30);
                    }
                }
            }
        }

        // On récupère l'instance du repository
        Repository::setConfig(__DIR__."/../../../config.ini");
        $repository = Repository::getInstance();

        // On récupère la liste des touites
        $touites = $repository->getTouiteUtilisateur("Martin");
        
        $tl = new TouiteList();

        foreach ($touites as $t){
            $tl->ajoutTouite($t);
        }

        // On crée le renderer pour afficher la liste
        $renderer = new TouiteListRenderer($tl);

        // On construit le HTML de la page d'accueil
        $html = '<h1>Accueil !</h1>';

        // Formulaire pour s'abonner à un tag
        $html .= '<h2>S\'abonner à un tag</h2>';
        $html .= '<form method="POST" action="?action=default">';
        $html .= '<label for="tag">Tag (sans #) :</label>';
        $html .= '<input type="text" id="tag" name="tag" required>';
        $html .= '<button type="submit" name="valider" value="1">S\'abonner</button>';
        $html .= '</form>';

        // On affiche les tags enregistrés
        if (count($tagsAbonnes) > 0) {
            $html .= '<p>Tags suivis : ';

            foreach ($tagsAbonnes as $tag) {
                $tagAffiche = filter_var($tag, FILTER_SANITIZE_SPECIAL_CHARS);
                $html .= '#' . $tagAffiche . ' ';
            }

            $html .= '</p>';
        }

        // On affiche la liste des touites
        $html .= $renderer->render();

        return $html;
    }
}
