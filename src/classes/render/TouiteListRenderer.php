
<?php

namespace touiteur\render;

use touiteur\message\TouiteList;

class TouiteListRenderer
{
    private TouiteList $touites;

    // On récupère la liste des touites à afficher
    public function __construct(TouiteList $touites)
    {
        $this->touites = $touites;
    }

    // Construit le HTML pour afficher les touites
    public function render(): string
    {
        // Si la liste est vide, on affiche un message
        if ($this->touites->getNbTouite() === 0) {
            return '<p class="liste-vide">Aucun touite à afficher.</p>';
        }

        $html = '<section class="liste-touites">';

        // On parcourt les touites de la liste
        foreach ($this->touites as $touite) {
            // On crée un renderer pour chaque touite
            $renderer = new TouiteRenderer($touite);

            // On ajoute son affichage court au HTML
            $html .= $renderer->renderCourt();
        }

        $html .= '</section>';

        return $html;
    }
}
