<?php

declare(strict_types=1);

namespace Market\Core;

use Market\Controller\AbstractController;
use Market\Exception\PageValidateException;
use Market\Exception\SubpageValidateException;
use Market\Exception\ValidateException;

class Controller extends AbstractController
{
    protected array $classList = [];

    public function run(): void
    {
        $this->classList = $this->request->getClassesList('\..\Controller');

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
        $this->classList = $this->request->getClassesList('\..\Controller\UserPanelController');

        $subpage = $this->subpage();

        if (!$this->ifPageExist($subpage)) {
            $subpage = $this->subpage;
        }

        $this->subpage = $subpage;

        $class = 'Market\\Controller\\UserPanelController\\' . $subpage . 'Controller';

        return $class;
    }

    private function catchValidateException(AbstractController $className)
    {
        try {
            $className->run();
            //Handle Errors throwing during exchange data between database and page
        } catch (PageValidateException $e) {
            $this->params['error'] = $e->getMessage();

            $this->view->render($this->page, $this->subpage, $this->params);
        } catch (SubpageValidateException $e) {
            $this->params['error'] = $e->getMessage();
            //errors about myAdv subpage
            if ($this->subpage === 'myAdv') {
                $this->myAdvhandleException($e);
            }

            //errors about myData
            if ($this->subpage === 'myData') {
                $this->params['uData'] = $this->readModel->getUserData();
                $this->params['listOfPlaces'] = $this->readModel->getListOfPlaces();
            }
            $this->view->render($this->page, $this->subpage, $this->params);
        } catch (ValidateException $e) {
            $this->params['error'] = $e->getMessage();

            if ($this->page === 'main') {
                $this->page = 'error';
            }
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
            $countOfAdverts = $this->readModel->getCountUserAdvertisements();
            $this->params['countOfPages'] = (int) ceil($countOfAdverts / 20);
            $this->params['userAdverts'] = ($countOfAdverts > 0) ? $this->readModel->getUserAdvertisements() : null;
        }
    }

    private function ifPageExist(string $className): bool
    {
        $exist = false;
        foreach ($this->classList as $value) {
            $value = str_replace('Controller', '', $value);
            $value = lcfirst($value);
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
