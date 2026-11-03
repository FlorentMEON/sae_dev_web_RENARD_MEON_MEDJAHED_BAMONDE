<?php
declare(strict_types=1);
namespace touiteur;
use touiteur\dispatch as DP;
use touiteur\action as AC;

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('touiteur\\', __DIR__."/classes");

$dis = new DP\Dispatcher();

$dis->addRoute('default', AC\DefaultAction::class);
    
$dis->run();