<?php

declare(strict_types=1);

namespace touiteur\action;
use touiteur\repository\Repository;

class FollowTagAction extends Action
{
    public function execute(): string
    {

        // On récupère l'instance du repository
        Repository::setConfig(__DIR__ . "/../../../config.ini");
        $repository = Repository::getInstance();
        
        session_start();

        // tableau des tags auquels l'utilisateur est abonné
        $tagsAbonnes = [];

        if (isset($_SESSION['username'])){
            $tagsAbonnes = $_SESSION['tagsSuivis'] ?? [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Abonnement à un tag
                if (isset($_POST['tag']) && $_POST['tag'] !== '') {
                    $nouveauTag = "#".filter_var($_POST['tag'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }

            } else {
                if (isset($_GET['tag']) && $_GET['tag'] !== '') {
                    $nouveauTag = "#".filter_var($_GET['tag'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
            }

            $id_tag = $repository->getIdTag($nouveauTag);

            if ($id_tag !== false){ // On vérifie l'existance du tag
                // On vérifie que le tag n'est pas déjà enregistré
                $dejaAbonne = false;

                foreach ($tagsAbonnes as $tag) {
                    if ($tag === $nouveauTag) {
                        $dejaAbonne = true;
                    }
                }

                // Si le tag est nouveau, on l'ajoute
                if (!$dejaAbonne) {
                    $tagsAbonnes[] = $nouveauTag;
                    // On enregistre tous les tags dans la session
                    $_SESSION["tagsSuivis"] = $tagsAbonnes;
                    $repository->setAbonnementTag($_SESSION['username'], $id_tag['id_tag']);
                }
            }
        }

        header('Location: ?action=default');
        exit;
    }
}