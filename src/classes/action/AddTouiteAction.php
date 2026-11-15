<?php
declare(strict_types=1);

namespace touiteur\action;

use touiteur\message as AL;
use touiteur\repository\Repository;

class AddTouiteAction extends Action
{
    public function execute(): string
    {
        session_start();

        if (isset($_SESSION['username'])) {

            if ($this->http_method === 'GET') {
                $res = <<<HTML
                <form method="post" action="?action=add_touite">
                    <label for="touite">Rédigez votre touite</label>
                    <br>
                    <textarea
                        id="touite"
                        name="touite"
                        rows="5"
                        cols="33"
                        minlength="1"
                        maxlength="235"
                        required></textarea>
                    <br>
                    <button type="submit">Envoyer</button>
                </form>
                HTML;
            }

            if ($this->http_method === 'POST') {

                Repository::setConfig(__DIR__ . "/../../../config.ini");
                $repository = Repository::getInstance();

                $nom = $_SESSION['username'];
                $touite = filter_var(
                    $_POST['touite'],
                    FILTER_SANITIZE_FULL_SPECIAL_CHARS
                );

                $id_touite = $repository->ajoutTouite($nom, $touite);

                $t = new AL\Touite($touite, $nom);
                $tags = $t->getTags();

                foreach ($tags as $tag) {
                    $id_tag = $repository->getIdTag($tag);

                    if ($id_tag === false) {
                        $id_tag = $repository->ajoutTag($tag);
                    } else {
                        $id_tag = $id_tag['id_tag'];
                    }

                    $repository->ajouterTagTouite($id_touite, $id_tag);
                }

                header('Location: ?action=default');
                exit;
            }
            return $res;
        }

        header('Location: ?action=default');
        exit;
    }
}
