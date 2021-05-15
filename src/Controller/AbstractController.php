<?php

declare(strict_types=1);

namespace Market\Controller;

use Exception;
use Market\Model\CreateModel;
use Market\Model\DeleteModel;
use Market\Model\ReadModel;
use Market\Model\UpdateModel;
use Market\Core\Request;
use Market\Core\View;

abstract class AbstractController
{
    protected static array $configuration = [];

    protected string $page;
    protected string $subpage = '';
    protected array $params = [];

    protected CreateModel $createModel;
    protected ReadModel $readModel;
    protected UpdateModel $updateModel;
    protected DeleteModel $deleteModel;
    protected Request $request;
    protected View $view;

    //Initializing database configuration
    public static function initConfiguration(array $configuration): void
    {
        self::$configuration = $configuration;
    }

    public function __construct(string $page)
    {
        if (empty(self::$configuration['db'])) {
            throw new Exception("Błąd konfiguracji");
        }
        $this->page = $page;

        $this->createModel = new CreateModel(self::$configuration['db']);
        $this->readModel = new ReadModel(self::$configuration['db']);
        $this->updateModel = new UpdateModel(self::$configuration['db']);
        $this->deleteModel = new DeleteModel(self::$configuration['db']);

        $this->request = new Request();
        $this->view = new View();
    }

    protected function logout(): void
    {
        session_destroy();
        header("Location: /?action=main");
        exit;
    }

    abstract public function run(): void;
}
