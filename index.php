<?php

declare(strict_types=1);

//Autoloader
spl_autoload_register(function (string $classNamespace) {
    $path = str_replace(['\\', 'Market'], ['/', ''], $classNamespace);
    $path = "src/$path.php";
    require($path);
});

require_once("src/Utils/debug.php");
$configuration = require_once("config/config.php");

use Market\Controller\AbstractController;
use Market\Core\Controller;
use Market\Exception\DatabaseException;

session_start();

try {
    AbstractController::initConfiguration($configuration);

    (new Controller('main'))->run();
} catch (DatabaseException $e) {
    echo '<h3>Wystąpił problem z Aplikacją. Spróbuj ponownie za chwilę.<h3>';
    echo $e->getMessage();
    dump($e);
} catch (\Throwable $e) {
    echo '<h3>Wystąpił błąd w aplikacji<h3>';
    echo $e->getMessage();
    dump($e);
}
