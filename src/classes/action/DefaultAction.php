
<?php

declare(strict_types=1);

namespace touiteur\action;

use touiteur\repository\Repository;
use touiteur\render\TouiteListRenderer;

class DefaultAction extends Action
{
    public function execute(): string
    {
        // On crée le repository
        $repository = new Repository();

        // On récupère la liste des touites
        $touites = $repository->getTouite();

        // On crée le renderer pour afficher les touites
        $renderer = new TouiteListRenderer($touites);

        // On construit le HTML de l'accueil
        $html = '<h1>Accueil !</h1>';
        $html .= $renderer->render();

        return $html;
    }
}
