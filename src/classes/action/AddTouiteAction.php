<?php
declare(strict_types=1);

namespace touiteur\action;
use touiteur\render as TR;
use touiteur\message as AL;
use touiteur\repository\Repository;




class AddTouiteAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            $res = <<<HTML
            <form method="post" action="?action=add-touite">
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" required>
                </br>
                <label for="touite">touite</label>
                </br>
                <textarea type="textarea" 
                id="touite"
                name="touite" 
                rows="5"
                cols="33"
                maxlength=235
                required>
            </textarea>
                </br>
                <button type ="submit">envoyé</button>
            </form>
            HTML;
        }

        if ($this->http_method === 'POST') {
            $repository = Repository::getInstance();

            $nom = filter_var(
                $_POST['name'] ?? '',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS
            );

            $touite = filter_var(
                $_POST['touite'] ?? '',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS
            );

            if ($repository->getUSer('name')==''){
                $repository->addUser($nom);
            }
            
            $repository->addTweet($nom,$touite);

        }
            $res = "Nom:$nom</br>touite:$touite";
        
        return $res;
    }
}