<?php

declare(strict_types=1);

namespace touiteur\action;
use touiteur\repository\Repository;

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

                Repository::setConfig(__DIR__."/../../../config.ini");
                $repository = Repository::getInstance();

                if ($repository->getIdUtilisateur($username) === false){
                    $repository->ajoutUtilisateur($username);
                    $_SESSION["utisSuivis"] = [];
                    $_SESSION["tagsSuivis"] = [];
                } else {
                    $_SESSION["utisSuivis"] = $repository->getUtilisateurSuivi($username);
                    $_SESSION["tagsSuivis"] = $repository->getTagsSuivi($username);
                }

                header('Location: ?action=default');
                exit;
            }
        } 
        return $res;
    }
}