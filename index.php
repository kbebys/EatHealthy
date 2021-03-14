<?php

declare(strict_types=1);

//Autoloader
spl_autoload_register(function (string $classNamespace) {
    //str_rplace replaces sings in string|array
    $path = str_replace(['\\', 'Market'], ['/', ''], $classNamespace);
    $path = "src/$path.php";
    require($path);
});

require_once("src/Utils/debug.php");
$configuration = require_once("config/config.php");

use Market\AbstractController;
use Market\Controller;
use Market\Request;

$request = new Request($_GET, $_POST);

try {
    AbstractController::initConfiguration($configuration);
    (new Controller($request))->run();
} catch (\Throwable $e) {
    echo '<h3>Wystąpił błąd w aplikacji: ' . $e->getMessage() . '<h3>';
    dump($e);
}
