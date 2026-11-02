<?php
declare(strict_types=1);

namespace touiteur\action;

class DefaultAction extends Action{
    public function execute() : string{
        return "<h1>Accueil !</h1>";
    }
}