<?php

declare(strict_types=1);

namespace Market\Controller;

use Exception;
use Market\Exception\ErrorException;
use Market\Model\AbstractModel;
use Market\Model\CreateModel;
use Market\Model\DeleteModel;
use Market\Model\ReadModel;
use Market\Model\UpdateModel;
use Market\Request;
use Market\View;

abstract class AbstractController
{
    protected const DEFAULT_ACTION = 'main';

    private static array $configuration = [];

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

    public function __construct(Request $request)
    {
        if (empty(self::$configuration['db'])) {
            throw new Exception("Błąd konfiguracji");
        }

        AbstractModel::initConnection(self::$configuration['db']);
        $this->createModel = new CreateModel();
        $this->readModel = new ReadModel();
        $this->updateModel = new UpdateModel();
        $this->deleteModel = new DeleteModel();

        $this->request = $request;
        $this->view = new View();
    }

    //function to control content of pages
    public function run(): void
    {
        try {
            $action = $this->action();
            //if exist given action variable 
            if (!method_exists($this, $action)) {
                $action = self::DEFAULT_ACTION;
            }
            $this->$action();
        } catch (ErrorException $e) {
            $param['error'] = $e->getMessage();
            $this->view->render($action, '', $param ?? []);
        }
    }

    private function action(): string
    {
        return $this->request->getParam('action', self::DEFAULT_ACTION);
    }
}
