<?php

declare(strict_types=1);

namespace touiteur\action;

class ConnectionAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            $res = <<<HTML
            <form method="post" action="?action=connect">
                <label for="connection">Nom de l'utilisateur:</label>
                <input type="text" id="name" name="name" required>
                <button type ="submit">Se connecter</button>
            </form>
            HTML;
        }

        if ($this->http_method === 'POST'){
            if (isset($_POST['name'])){
                session_start();
                $username = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $_SESSION['username'] = $username;
                $res = "<p>vous etes connecté entant qu'utilisateur $username</p>";
                $res .= "</br><a href=\"?action=default\">Retour à l'accueil</a>";
            }
        } 
        return $res;
    }
}