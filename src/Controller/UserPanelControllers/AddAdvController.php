<?php

declare(strict_types=1);

namespace Market\Controller\UserPanelControllers;

use Market\Controller\AbstractController;

class AddAdvController extends AbstractController
{
    public function run(): void
    {
        $this->subpage = 'addAdv';
        //If user did not add its personal data
        if (!$this->readModel->getUserData()) {
            $this->subpage = 'myData';
        }

        $savePost = $this->request->postParam('save');
        if ($savePost) {
            $advData = [
                'title' => $this->request->postParam('title'),
                'kind' => $this->request->postParam('kind'),
                'content' => $this->request->postParam('content'),
                'place' => $this->request->postParam('place')
            ];

            if ($this->createModel->addAdvertisment($advData) === true) {
                $this->params['messageWindow'] = 'Dodałeś ogłoszenie';
            }
        }
        $this->view->render($this->page, $this->subpage, $this->params);
    }
}
