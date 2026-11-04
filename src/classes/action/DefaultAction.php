<?php

declare(strict_types=1);

namespace touiteur\action;

use touiteur\repository\Repository;
use touiteur\render\TouiteListRenderer;

class DefaultAction extends Action
{
    public function execute(): string
    {
        // On récupère l'instance du repository
        $repository = Repository::getInstance();

        // On récupère la liste des touites
        $touites = $repository->getTouite();

        // On crée le renderer pour afficher la liste
        $renderer = new TouiteListRenderer($touites);

        // On construit le HTML de la page d'accueil
        $html = '<h1>Accueil !</h1>';
        $html .= $renderer->render();

        return $html;
    }
}
