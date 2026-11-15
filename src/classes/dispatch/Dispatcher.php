<?php
declare(strict_types=1);

namespace touiteur\dispatch;
use touiteur\action as AC;

class Dispatcher{
    private $action;
    private array $route = [];

    public function __construct(){
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void{
        $class = $this->route[$this->action] ?? AC\DefaultAction::class;
        $act = new $class();
        $html = $act->execute();
        $this->renderPage($html);
    }
    
    private function renderPage(String $html): void{
        $page = '<!DOCTYPE html>';
        $page .= '<html lang="fr">';
        $page .= '<head>';
        $page .= '<meta charset="UTF-8">';
        $page .= '<link rel="stylesheet" href="style.css">';
        $page .= '<title>Touiteur</title>';
        $page .= '</head>';
        $page .= '<body>';
        $page .= '<header>';
        $page .= '<h1>Touiteur</h1>';
        $page .= '</header>';
        $page .= '<main>';
        $page .= $html;
        $page .= '</main>';
        $page .= '</body>';
        $page .= '</html>';

        echo $page;
    }

    public function addRoute(String $action, String $class): self{
        $this->route[$action] = $class;
        return $this;
    }
}