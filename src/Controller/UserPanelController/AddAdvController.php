<?php

declare(strict_types=1);

namespace Market\Controller\UserPanelController;

use Market\Controller\AbstractController;

class AddAdvController extends AbstractController
{
    public function run(): void
    {
        $this->subpage = 'addAdv';
        //If user did not add its personal data
        $this->IfUserDataNotExist();

        $savePost = $this->request->postParam('save');
        if ($savePost) {
            $this->addNewAdvertisement();
        }
        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function IfUserDataNotExist(): void
    {
        if (!$this->readModel->getUserData()) {
            $this->subpage = 'myData';
        }
    }

    private function addNewAdvertisement(): void
    {
        $advData = [
            'title' => $this->request->postParam('title'),
            'kind' => $this->request->postParam('kind'),
            'content' => $this->request->postParam('content'),
            'place' => $this->request->postParam('place')
        ];

        if ($this->createModel->addAdvertisement($advData) === true) {
            $this->params['messageWindow'] = 'Dodałeś ogłoszenie';
        }
    }
}
