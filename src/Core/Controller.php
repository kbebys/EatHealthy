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
        //Set list of avaiable controller classes
        //Each class is called like page
        $this->classList = $this->request->getPagesList('\..\..\templates\pages');

        $page = $this->page();

        if ($page === 'logout') {
            $this->logout();
        }

        if (!$this->ifClassExist($page)) {
            $page = $this->page;
        }

        $this->page = $page;

        if ($page === 'userPanel') {
            $this->chooseActionForUserPanael();
            exit;
        }

        $class = 'Market\\Controller\\' . $page . 'Controller';

        try {
            (new $class($this->page))->run();
        } catch (ErrorException $e) {
            $this->params['error'] = $e->getMessage();
            $this->view->render($this->page, $this->subpage, $this->params);
        }
    }

    public function chooseActionForUserPanael(): void
    {
        //If someone try to enter user panel without login
        if (!isset($_SESSION['loggedin'])) {
            header("Location: /?action=main");
            exit;
        }

        //default value for subpage
        $this->subpage = 'myAdv';

        $this->classList = $this->request->getPagesList('\..\..\templates\pages\subpages');

        $subpage = $this->subpage();

        if (!$this->ifClassExist($subpage)) {
            $subpage = $this->subpage;
        }

        $this->subpage = $subpage;

        $class = 'Market\\Controller\\UserPanelControllers\\' . $subpage . 'Controller';

        try {
            (new $class($this->page))->run();
        } catch (ErrorException $e) {
            //Handle Errors throwing during exchange data between database and page
            $this->params['errorWindow'] = $e->getMessage();
            $this->view->render($this->page, $this->subpage, $this->params);
        }
    }

    //Get information wich page chose user
    private function page(): string
    {
        return $this->request->getParam('action', $this->page);
    }

    //Get information wich subpage chose user
    private function subpage(): string
    {
        return $this->request->getParam('subpage', $this->subpage);
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
}
