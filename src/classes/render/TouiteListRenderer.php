
<?php

namespace touiteur\render;

class TouiteListRenderer
{
    private array $touites;

    public function __construct(array $touites)
    {
        $this->touites = $touites;
    }

    public function render(): string
    {
        if (count($this->touites) === 0) {
            return '<p class="liste-vide">Aucun touite à afficher.</p>';
        }

        $html = '<section class="liste-touites">';

        foreach ($this->touites as $touite) {
            $renderer = new TouiteRenderer($touite);
            $html .= $renderer->renderCourt();
        }

        $html .= '</section>';

        return $html;
    }
}
