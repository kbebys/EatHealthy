<?php

declare(strict_types=1);

namespace Market\Controller\UserPanelControllers;

use Market\Controller\AbstractController;

class DeleteAccController extends AbstractController
{
    public function run(): void
    {
        $this->subpage = 'deleteAcc';
        $save = $this->request->postParam('save');

        if ($save) {
            switch ($save) {
                case 'tak':
                    $this->deleteModel->deleteAcc();
                    $this->logout();
                    break;
                case 'usuń':
                    $password = $this->request->postParam('password');
                    if ($this->readModel->checkPassword($password) === true) {
                        $this->params['confirm'] = true;
                    }
                    break;
                    dump($this->params);
            }
        }
        $this->view->render($this->page, $this->subpage, $this->params);
    }
}
