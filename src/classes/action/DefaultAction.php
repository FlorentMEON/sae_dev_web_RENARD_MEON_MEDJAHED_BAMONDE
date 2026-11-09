
<?php

declare(strict_types=1);

namespace touiteur\action;

use touiteur\repository\Repository;
use touiteur\render\TouiteListRenderer;

class DefaultAction extends Action
{
    public function execute(): string
    {
        // On récupère le tag déjà enregistré dans le cookie
        $tagAbonne = $_COOKIE['tag_abonne'] ?? '';

        // Si le formulaire a été envoyé, on enregistre le tag
        if (isset($_POST['tag']) && $_POST['tag'] !== '') {
            $tagAbonne = $_POST['tag'];

            // Le cookie est conservé pendant 30 jours
            setcookie('tag_abonne', $tagAbonne, time() + 60 * 60 * 24 * 30);
        }

        // On récupère l'instance du repository
        $repository = Repository::getInstance();

        // On récupère la liste des touites
        $touites = $repository->getTouite();

        // On crée le renderer pour afficher la liste
        $renderer = new TouiteListRenderer($touites);

        // On construit le HTML de la page d'accueil
        $html = '<h1>Accueil !</h1>';

        // Formulaire pour s'abonner à un tag
        $html .= '<h2>S\'abonner à un tag</h2>';
        $html .= '<form method="POST" action="?action=default">';
        $html .= '<label for="tag">Tag :</label>';
        $html .= '<input type="text" id="tag" name="tag" required>';
        $html .= '<button type="submit">S\'abonner</button>';
        $html .= '</form>';

        // On affiche le tag enregistré dans le cookie
        if ($tagAbonne !== '') {
            $tagAffiche = filter_var($tagAbonne, FILTER_SANITIZE_SPECIAL_CHARS);
            $html .= '<p>Tag enregistré : #' . $tagAffiche . '</p>';
        }

        // On affiche la liste des touites
        $html .= $renderer->render();

        return $html;
    }
}
