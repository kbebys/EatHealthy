<?php

declare(strict_types=1);

namespace Market\Controller\UserPanelControllers;

use Market\Controller\AbstractController;
use Market\Exception\ErrorException;

class MyAdvController extends AbstractController
{
    public function run(): void
    {
        $this->subpage = 'myAdv';
        try {

            $advertOption = $this->userAdvertOption();
            $advertOption = $advertOption . 'UserAdvert';

            $idAdv = (int) $this->request->getParam('id');

            if (!method_exists($this, $advertOption)) {
                $advertOption = 'userAdvert';
            }
            $this->$advertOption($idAdv);
        } catch (ErrorException $e) {
            $this->params['errorWindow'] = $e->getMessage();

            //Code === 2 when error about editing advertisement is throwing
            if ($e->getCode() === 2) {
                $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
            } else {
                $this->params['userAdverts'] = $this->readModel->getUserAdvertisements();
            }

            $this->view->render($this->page, $this->subpage, $this->params);
        }
    }

    //display details of chosen advertisement
    private function detailsUserAdvert(int $idAdv): void
    {
        $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
        $this->view->render($this->page, $this->subpage, $this->params);
    }


    private function userAdvert($idAdv): void
    {
        $countOfAdverts = $this->readModel->getCountUserAdvertisements();
        //when user doesn't have adverts don't get them from database
        if ($countOfAdverts !== 0) {
            $this->params['userAdverts'] = $this->readModel->getUserAdvertisements();
        }

        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function editUserAdvert(int $idAdv): void
    {
        $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);

        //It is flag uses to display editing view
        $this->params['edit'] = true;

        if ($this->request->postParam('save')) {
            $advData = [
                'title' => $this->request->postParam('title'),
                'kind' => $this->request->postParam('kind'),
                'content' => $this->request->postParam('content'),
                'place' => $this->request->postParam('place'),
            ];

            if ($this->updateModel->changeAdvertisment($advData, $idAdv) === true) {
                $this->params['edit'] = null;
                $this->params['messageWindow'] = 'Twoje ogłoszenie zostało zmienione';
                $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
            }
        }
        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function deleteUserAdvert(int $idAdv): void
    {
        //Confirmation if User really wants to delete advertisement
        $ifDelete = $this->request->getParam('question');

        $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);

        if ($ifDelete === 'yes') {
            if ($this->deleteModel->deleteUserAdvertisment($idAdv) === true) {
                $this->params = [
                    'messageWindow' => 'Ogłoszenie zostało usunięte',
                    'userAdverts' => $this->readModel->getUserAdvertisements()
                ];
            }
        } elseif ($ifDelete === 'no') {
            //Situation when user resign from deleting 
            $this->params['delete'] = false;
        } else {
            //situation before confirmation. When User clicked delete
            $this->params['delete'] = true;
        }
        $this->view->render($this->page, $this->subpage, $this->params);
    }

    //Get what option chose User in my advertisments subpage
    private function userAdvertOption(): string
    {
        return $this->request->getParam('advertOption', '');
    }
}
