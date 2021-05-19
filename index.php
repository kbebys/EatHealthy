<?php

declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

require_once("src/Utils/debug.php");
$configuration = require_once("config/config.php");

use Market\Controller\AbstractController;
use Market\Core\Controller;
use Market\ErrorHandler\ErrorHandler;
use Market\Exception\DatabaseException;

session_start();

try {
    AbstractController::initConfiguration($configuration);

    (new Controller('main'))->run();
} catch (DatabaseException $e) {
    echo '<h3>Wystąpił problem z Aplikacją. Spróbuj ponownie za chwilę.<h3>';
    echo $e->getMessage();
    dump($e);
    (new ErrorHandler())->errorLog($e, 'databaseException');
} catch (Throwable $e) {
    echo '<h3>Wystąpił błąd w aplikacji<h3>';
    echo $e->getMessage();
    dump($e);
    (new ErrorHandler())->errorLog($e, 'throwable');
}
