<?php

declare(strict_types=1);

namespace Market\Controller\UserPanelController;

use Market\Controller\AbstractController;

class MyAdvController extends AbstractController
{
    private int $idAdv;

    private string $advertOption = 'getUserAdverts';

    private const PAGE_SIZE = 20;

    public function run(): void
    {
        $this->subpage = 'myAdv';

        $this->chosenOption();

        $this->idAdv = (int) $this->request->getParam('id');

        $this->{$this->advertOption}();

        $this->view->render($this->page, $this->subpage, $this->params);
    }

    //get all of the user advertisement
    private function getUserAdverts(): void
    {
        $countOfAdverts = $this->readModel->getCountUserAdvertisements();

        //when user doesn't have adverts don't get them from database
        if ($countOfAdverts !== 0) {
            $countOfPages = (int) ceil($countOfAdverts / self::PAGE_SIZE);
            $pageNumber = $this->getPageNumber();
            $pageNumber = (($pageNumber > $countOfPages) || ($pageNumber < 1)) ? 1 : $pageNumber;

            $this->params['pageNumber'] = $pageNumber;
            $this->params['countOfPages'] = $countOfPages;
            $this->params['userAdverts'] = $this->readModel->getUserAdvertisements($this->params['pageNumber'], self::PAGE_SIZE);
        }
    }

    //display details of chosen advertisement
    private function detailsUserAdvert(): void
    {
        $this->params['userAdvert'] = $this->readModel->getUserAdvertisement($this->idAdv);
    }


    private function editUserAdvert(): void
    {
        $this->detailsUserAdvert();

        //It is flag uses to display editing view
        $this->params['edit'] = true;

        if ($this->request->postParam('save')) {
            $advData = [
                'title' => $this->request->postParam('title'),
                'kind' => $this->request->postParam('kind'),
                'content' => $this->request->postParam('content'),
            ];

            if ($this->updateModel->changeUserAdvertisement($advData, $this->idAdv) === true) {
                $this->params['edit'] = null;
                $this->params['success'] = 'Twoje og??oszenie zosta??o zmienione';
                $this->detailsUserAdvert();
            }
        }
    }

    private function deleteUserAdvert(): void
    {
        //Confirmation if User really wants to delete advertisement
        $ifDelete = $this->request->getParam('question');

        $this->detailsUserAdvert();

        if ($ifDelete === 'yes') {
            if ($this->deleteModel->deleteUserAdvertisement($this->idAdv) === true) {
                $this->params = ['success' => 'Og??oszenie zosta??o usuni??te'];
                $this->getUserAdverts();
            }
        } elseif ($ifDelete === 'no') {
            //Situation when user resign from deleting 
            $this->params['delete'] = false;
        } else {
            //situation before confirmation. When User clicked delete
            $this->params['delete'] = true;
        }
    }

    //Which option user choose
    private function chosenOption(): void
    {
        $advertOption = $this->userAdvertOption();
        $advertOption = $advertOption . 'UserAdvert';

        if (method_exists($this, $advertOption)) {
            $this->advertOption = $advertOption;
        }
    }
    //Get what option chose User in my advertisements subpage
    private function userAdvertOption(): string
    {
        return $this->request->getParam('advertOption', '');
    }

    //Get wich page with advertisements is display
    private function getPageNumber(): int
    {
        return (int) $this->request->getParam('pageNumber', 1);
    }
}
