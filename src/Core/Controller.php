<?php

declare(strict_types=1);

namespace Market\Core;

use Market\Controller\AbstractController;
use Market\Exception\ErrorException;
use Market\Exception\PageValidateException;
use Market\Exception\SubpageValidateException;
use Market\Exception\ValidateException;

class Controller extends AbstractController
{
    protected array $classList = [];

    public function run(): void
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
            $this->runUserPanael();
            exit;
        }

        $class = 'Market\\Controller\\' . $page . 'Controller';

        $this->catchValidateException(new $class($this->page));

        // try {
        //     (new $class($this->page))->run();
        // } catch (ValidateException $e) {
        //     $this->params['error'] = $e->getMessage();
        //     $this->view->render($this->page, $this->subpage, $this->params);
        // }
    }

    public function runUserPanael(): void
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

        $this->catchValidateException(new $class($this->page));

        // try {
        //     (new $class($this->page))->run();
        // } catch (PageValidateException $e) {
        //     //Handle Errors throwing during exchange data between database and page
        //     $this->params['errorWindow'] = $e->getMessage();
        //     $this->view->render($this->page, $this->subpage, $this->params);
        // } catch (SubpageValidateException $e) {
        //     $this->params['errorWindow'] = $e->getMessage();

        //     //errors about myAdv subpage
        //     if ($this->subpage === 'myAdv') {
        //         //Code === 2 when error about editing advertisement is throwing
        //         if ($e->getCode() === 2) {
        //             $idAdv = (int) $this->request->getParam('id');
        //             $this->params['edit'] = true;
        //             $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
        //         } else {
        //             $this->params['userAdverts'] = $this->readModel->getUserAdvertisements();
        //         }
        //     }
        //     $this->view->render($this->page, $this->subpage, $this->params);
        // }
    }

    public function catchValidateException(AbstractController $className)
    {
        try {
            $className->run();
        } catch (PageValidateException $e) {
            //Handle Errors throwing during exchange data between database and page
            $this->params['errorWindow'] = $e->getMessage();
            $this->view->render($this->page, $this->subpage, $this->params);
        } catch (SubpageValidateException $e) {
            $this->params['errorWindow'] = $e->getMessage();

            //errors about myAdv subpage
            if ($this->subpage === 'myAdv') {
                //Code === 2 when error about editing advertisement is throwing
                if ($e->getCode() === 2) {
                    $idAdv = (int) $this->request->getParam('id');
                    $this->params['edit'] = true;
                    $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
                } else {
                    $this->params['userAdverts'] = $this->readModel->getUserAdvertisements();
                }
            }
            $this->view->render($this->page, $this->subpage, $this->params);
        } catch (ValidateException $e) {
            $this->params['error'] = $e->getMessage();
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
