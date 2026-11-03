
<?php

namespace src\classes\renderer;

use src\classes\message\Touite\Touite;

class TouiteRenderer
{
    // Choix d'affichage, pas une contrainte du sujet
    const LONGUEUR_COURTE = 150;

    private Touite $touite;

    public function __construct(Touite $touite)
    {
        $this->touite = $touite;
    }

    public function renderCourt(): string
    {
        $texte = $this->touite->texte;

        if (mb_strlen($texte, 'UTF-8') > self::LONGUEUR_COURTE) {
            $texte = mb_substr($texte, 0, self::LONGUEUR_COURTE, 'UTF-8') . '…';
        }

        $html = '<article class="touite touite-court">';
        $html .= '<p class="touite-auteur">' . $this->lienAuteur() . '</p>';
        $html .= '<p class="touite-texte">' . $this->echapper($texte) . '</p>';
        $html .= $this->renderTags();
        $html .= '</article>';

        return $html;
    }

    public function renderDetail(): string
    {
        $html = '<article class="touite touite-detail">';
        $html .= '<p class="touite-auteur">' . $this->lienAuteur() . '</p>';
        $html .= '<p class="touite-texte">' . $this->echapper($this->touite->texte) . '</p>';
        $html .= $this->renderTags();
        $html .= '</article>';

        return $html;
    }

    private function renderTags(): string
    {
        $liens = '';

        foreach ($this->touite->getTags() as $tag) {
            $nom = ltrim($tag, '#');
            $url = 'index.php?action=tag&tag=' . urlencode($nom);
            $liens .= $this->creerLien($url, '#' . $nom) . ' ';
        }

        if ($liens === '') {
            return '';
        }

        return '<p class="touite-tags">' . $liens . '</p>';
    }

    private function lienAuteur(): string
    {
        $pseudo = $this->touite->utilisateur;
        $url = 'index.php?action=user&pseudo=' . urlencode($pseudo);

        return $this->creerLien($url, $pseudo);
    }

    // urlencode() est déjà appliqué aux paramètres ; on échappe ici l'URL entière
    private function creerLien(string $url, string $libelle): string
    {
        return '<a href="' . $this->echapper($url) . '">' . $this->echapper($libelle) . '</a>';
    }

    private function echapper(string $texte): string
    {
        return htmlspecialchars($texte, ENT_QUOTES, 'UTF-8');
    }
}
