<?php

declare(strict_types=1);

namespace touiteur\action;
use touiteur\repository\Repository;

class DeconnectionAction extends Action
{
    public function execute(): string
    {
        session_start();
        if (isset($_SESSION['username'])){
            session_destroy();
        }

        header('Location: ?action=default');
        exit;
    }
}