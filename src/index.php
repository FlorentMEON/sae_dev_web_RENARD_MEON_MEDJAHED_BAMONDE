<?php
declare(strict_types=1);
namespace touiteur;
use touiteur\dispatch as DP;
use touiteur\action as AC;
use touiteur\message as MS;
use touiteur\render as RD;

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('touiteur\\', __DIR__."/classes");

$dis = new DP\Dispatcher();

$dis->addRoute('default', AC\DefaultAction::class)
    ->addRoute('connect', AC\ConnectionAction::class)
    ->addRoute('add_touite', AC\AddTouiteAction::class)
	->addRoute('add_tag', AC\FollowTagAction::class)
    ->addRoute('add_user', AC\FollowUserAction::class)
    ->addRoute('show_user', AC\ShowUserAction::class)
    ->addRoute('show_tag', AC\ShowTagAction::class)
    ->addRoute('show_touite', AC\ShowTouiteAction::class)
    ->addRoute('show_influenceurs', AC\ShowInfluenceursAction::class)
    ->addRoute('disconnect', AC\DeconnectionAction::class);
    
$dis->run();