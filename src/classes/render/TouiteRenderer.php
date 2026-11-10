<?php

namespace touiteur\render;

use touiteur\message\Touite;

class TouiteRenderer
{
    // Limites pour l'affichage des touites
    const LONGUEUR_COURTE = 150;
    const LONGUEUR_MAX = 235;

    private Touite $touite;

    // On récupère le touite à afficher
    public function __construct(Touite $touite)
    {
        $this->touite = $touite;
    }

    // Affiche une version courte du touite
    public function renderCourt(): string
    {
        // On récupère le texte et on le raccourcit si nécessaire
        $texte = $this->couper($this->touite->texte);

        // Construction du HTML du touite
        $html = '<article class="touite touite-court">';
        $html .= '<div class="touite-auteur">' . $this->lienAuteur() . '</div>';
        $html .= '<p class="touite-texte">' . $this->echapper($texte) . '</p>';
        $html .= $this->renderTags();
        $html .= '</article>';

        return $html;
    }

    // Affiche le touite en entier
    public function renderDetail(): string
    {
        $html = '<article class="touite touite-detail">';
        $html .= '<div class="touite-auteur">' . $this->lienAuteur() . '</div>';
        $html .= '<p class="touite-texte">'
            . $this->echapper($this->touite->texte) . '</p>';
        $html .= $this->renderTags();
        $html .= '</article>';

        return $html;
    }

    // Raccourcit le texte à 150 caractères sans couper un mot
    private function couper(string $texte): string
    {
        if (strlen($texte) <= self::LONGUEUR_COURTE) {
            return $texte;
        }

        // On sépare le texte en mots
        $mots = explode(' ', $texte);
        $court = '';

        // On ajoute les mots tant qu'on ne dépasse pas la limite
        foreach ($mots as $mot) {
            if ($court === '') {
                $nouveau = $mot;
            } else {
                $nouveau = $court . ' ' . $mot;
            }

            // Si le prochain mot dépasse la limite, on s'arrête
            if (strlen($nouveau) > self::LONGUEUR_COURTE) {
                if ($court === '') {
                    return $texte;
                }

                return $court . '...';
            }

            $court = $nouveau;
        }

        return $court;
    }

    // Enlève le # au début d'un tag s'il est présent
    private function enleverDieze(string $tag): string
    {
        if ($tag === '' || $tag[0] !== '#') {
            return $tag;
        }

        $nom = '';

        for ($i = 1; $i < strlen($tag); $i++) {
            $nom .= $tag[$i];
        }

        return $nom;
    }

    // Affiche les tags du touite
    private function renderTags(): string
    {
        $liens = '';

        foreach ($this->touite->getTags() as $tag) {
            $nom = $this->enleverDieze($tag);

            // L'action tag sera traitée par le Dispatcher
            $url = '?action=tag&tag=' . $nom;

            $liens .= $this->creerLien($url, '#' . $nom) . ' ';
        }

        // Si le touite n'a pas de tag, on n'affiche rien
        if ($liens === '') {
            return '';
        }

        return '<p class="touite-tags">' . $liens . '</p>';
    }

    // Permet de suivre l'auteur en cliquant sur son pseudo
    private function lienAuteur(): string
    {
        $pseudo = $this->echapper($this->touite->utilisateur);

        $html = '<form method="POST" action="?action=default">';
        $html .= '<button type="submit" name="suivrePseudo" value="' . $pseudo . '">';
        $html .= $pseudo;
        $html .= '</button>';
        $html .= '</form>';

        return $html;
    }

    // Crée une balise HTML pour un lien
    private function creerLien(string $url, string $libelle): string
    {
        return '<a href="' . $this->echapper($url) . '">'
            . $this->echapper($libelle) . '</a>';
    }

    // Sécurise le texte affiché
    private function echapper(string $texte): string
    {
        return filter_var($texte, FILTER_SANITIZE_SPECIAL_CHARS);
    }
}
