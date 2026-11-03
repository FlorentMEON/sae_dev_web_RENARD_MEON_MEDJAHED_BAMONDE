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
        $page = <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Touiteur</title>
            </head>
            <body>
                <header>
                    <h1>Bienvenu sur Touiteur (piou-piou)</h1>

                    <nav>
                        <ul>
                            <li><a href="?action=default">Accueil</a></li>
                        </ul>
                    </nav>
                </header>

                <main>
                    $html
                </main>
            </body>
            </html>
        HTML;

        echo $page;
    }

    public function addRoute(String $action, String $class): self{
        $this->route[$action] = $class;
        return $this;
    }
}