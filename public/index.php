<?php

use App\Controller\GameController;
use Symfony\Component\HttpFoundation\Request;


require __DIR__ .'/../vendor/autoload.php';

// xdebug_info();

$Request = Request::createFromGlobals();
$GameController = GameController::create();
$GameController->routing($Request);
// die("mea");

// $GameController->home();