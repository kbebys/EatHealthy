<?php

declare(strict_types=1);

namespace Market\Controller;

class LoginController extends AbstractController
{
    public function run(): void
    {
        if ($this->request->postParam('save')) {
            $this->loginUser();
        }
        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function loginUser(): void
    {
        $loginData = [
            'login' => $this->request->postParam('username'),
            'password' => $this->request->postParam('password')
        ];

        if ($this->readModel->login($loginData) === true) {
            $this->page = 'userPanel';
            $this->subpage = 'myAdv';
            $this->getUserAdvertisementsIfExist();
        }
    }

    private function getUserAdvertisementsIfExist(): void
    {
        $countUserAdverts = $this->readModel->getCountUserAdvertisements();
        if ($countUserAdverts !== 0) {
            $this->params['userAdverts'] = $this->readModel->getUserAdvertisements();
        }
    }
}
