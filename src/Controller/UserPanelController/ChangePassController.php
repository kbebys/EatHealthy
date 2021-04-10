<?php

declare(strict_types=1);

namespace Market\Controller\UserPanelController;

use Market\Controller\AbstractController;

class ChangePassController extends AbstractController
{
    public function run(): void
    {
        $this->subpage = 'changePass';

        if ($this->request->postParam('save')) {
            $passwords = [
                'old' => $this->request->postParam('password'),
                'new' => $this->request->postParam('new-password'),
                'newRepeat' => $this->request->postParam('new-repeat-password'),
            ];


            if ($this->updateModel->changePassword($passwords) === true) {
                $this->params['messageWindow'] = 'Hasło zostało zmienione';
            }
        }

        $this->view->render($this->page, $this->subpage, $this->params);
    }
}
