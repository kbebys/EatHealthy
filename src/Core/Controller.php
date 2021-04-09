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
        $this->classList = $this->request->getPagesList('\..\..\templates\pages');

        $page = $this->page();

        if ($page === 'logout') {
            $this->logout();
        }

        if (!$this->ifPageExist($page)) {
            $page = $this->page;
        }

        $this->page = $page;

        if ($page === 'userPanel') {
            $this->isLoggedin();
            //default value for subpage
            $this->subpage = 'myAdv';
            //What class will handle
            $class = $this->setClassForUserPanel();
        } else {

            $class = 'Market\\Controller\\' . $page . 'Controller';
        }
        //Create new instance of Controller and run it through the function catching exceptions
        $this->catchValidateException(new $class($this->page));
    }

    private function setClassForUserPanel(): string
    {
        $this->classList = $this->request->getPagesList('\..\..\templates\pages\subpages');

        $subpage = $this->subpage();

        if (!$this->ifPageExist($subpage)) {
            $subpage = $this->subpage;
        }

        $this->subpage = $subpage;

        $class = 'Market\\Controller\\UserPanelControllers\\' . $subpage . 'Controller';

        return $class;
    }

    private function catchValidateException(AbstractController $className)
    {
        try {
            $className->run();
            //Handle Errors throwing during exchange data between database and page
        } catch (PageValidateException $e) {
            //It is here because register and changePass use the same fun() 
            if ($this->page === 'register') {
                $this->params['error'] = $e->getMessage();
            } else {
                $this->params['errorWindow'] = $e->getMessage();
            }
            $this->view->render($this->page, $this->subpage, $this->params);
        } catch (SubpageValidateException $e) {
            $this->params['errorWindow'] = $e->getMessage();

            //errors about myAdv subpage
            if ($this->subpage === 'myAdv') {
                $this->myAdvhandleException($e);
            }
            $this->view->render($this->page, $this->subpage, $this->params);
        } catch (ValidateException $e) {
            $this->params['error'] = $e->getMessage();
            $this->view->render($this->page, $this->subpage, $this->params);
        }
    }

    private function myAdvhandleException(SubpageValidateException $e): void
    {
        //Code === 2 when error about editing advertisement is throwing
        if ($e->getCode() === 2) {
            $idAdv = (int) $this->request->getParam('id');
            $this->params['edit'] = true;
            $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
        } else {
            $this->params['userAdverts'] = $this->readModel->getUserAdvertisements();
        }
    }

    private function ifPageExist($className): bool
    {
        $exist = false;
        foreach ($this->classList as $value) {
            if ($className === $value) {
                $exist = true;
            }
        }
        return $exist;
    }



    private function isLoggedin(): void
    {
        //If someone try to enter user panel without login
        if (!isset($_SESSION['loggedin'])) {
            header("Location: /?action=main");
            exit;
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
}
