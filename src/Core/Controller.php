<?php

declare(strict_types=1);

namespace Market\Core;

use Market\Controller\AbstractController;
use Market\Exception\ErrorException;

class Controller extends AbstractController
{
    protected array $classList = [];

    public function chooseAction(): void
    {
        //set list of avaiable controller classes
        //Each class is called like page
        $this->classList = $this->request->getPagesList();

        $page = $this->action();

        if ($page === 'logout') {
            $this->logout();
        }

        if (!$this->ifClassExist($page)) {
            $page = $this->page;
        }

        $class = 'Market\\Controller\\' . $page . 'Controller';
        //set default page for each controller
        dump($this->page);
        try {
            (new $class($page))->run();
        } catch (ErrorException $e) {
            $param['error'] = $e->getMessage();
            $this->view->render($page, '', $param ?? []);
        }
    }

    private function action(): string
    {
        return $this->request->getParam('action', self::DEFAULT_ACTION);
    }

    private function ifClassExist($className): bool
    {
        $exist = false;
        foreach ($this->classList as $value) {
            if ($className === $value) {
                $exist = true;
            }
        }
        return $exist;
    }

    private function logout(): void
    {
        session_destroy();
        header("Location: /?action=main");
        exit;
    }
}
