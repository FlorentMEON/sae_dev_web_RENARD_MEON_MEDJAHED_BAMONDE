<?php

declare(strict_types=1);

namespace touiteur\action;
use touiteur\repository\Repository;

class FollowUserAction extends Action
{
    public function execute(): string
    {

        // On récupère l'instance du repository
        Repository::setConfig(__DIR__ . "/../../../config.ini");
        $repository = Repository::getInstance();
        
        session_start();

        // tableau des utilisateurs suivis par l'utilisateur courant
        $pseudosSuivis = $_SESSION['utisSuivis'] ?? [];

        // On vérifie que le formulaire a été envoyé en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Abonnement à un utilisateur
            if (isset($_POST['addUser']) && $_POST['addUser'] !== '') {
                $nouveauPseudo = $_POST['addUser'];

                // On vérifie que l'utilisateur n'est pas déjà suivi et la base de données
                $dejaSuivi = ($_SESSION['username'] === $nouveauPseudo);

                foreach ($pseudosSuivis as $pseudo) {
                    if ($pseudo === $nouveauPseudo) {
                        $dejaSuivi = true;
                    }
                }

                // Si l'utilisateur n'est pas déjà suivi, on l'ajoute
                if (!$dejaSuivi) {
                    $pseudosSuivis[] = $nouveauPseudo;

                    // On enregistre tous les pseudos dans la session et la base de données
                    $_SESSION["utisSuivis"] = $pseudosSuivis;
                    $repository->setSuiviUtilisateur($_SESSION['username'], $nouveauPseudo);
                }
            }
        }

        header('Location: ?action=default');
        exit;
    }
}