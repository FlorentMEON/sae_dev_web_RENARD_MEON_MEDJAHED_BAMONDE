
<?php

namespace touiteur\render;

class UserRenderer
{
    private string $utilisateur;
    private array $touites;

    public function __construct(string $utilisateur, array $touites)
    {
        $this->utilisateur = $utilisateur;
        $this->touites = $touites;
    }

    public function render(): string
    {
        $pseudo = htmlspecialchars($this->utilisateur, ENT_QUOTES, 'UTF-8');

        $html = '<h2>Touites de ' . $pseudo . '</h2>';

        $liste = new TouiteListRenderer($this->touites);
        $html .= $liste->render();

        return $html;
    }
}
