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

use Market\Controller\PageController;
use Market\Controller\AbstractController;
use Market\Controller\UserPanelController;
use Market\Exception\DatabaseException;
use Market\Request;

session_start();

$request = new Request($_GET, $_POST);

try {
    AbstractController::initConfiguration($configuration);

    if (isset($_SESSION['loggedin'])) {
        (new UserPanelController($request))->run();
    } else {
        (new PageController($request))->run();
    }
} catch (DatabaseException $e) {
    echo '<h3>Wystąpił problem z Aplikacją. Spróbuj ponownie za chwilę.<h3>';
    echo $e->getMessage();
    dump($e);
} catch (\Throwable $e) {
    echo '<h3>Wystąpił błąd w aplikacji<h3>';
    echo $e->getMessage();
    dump($e);
}
