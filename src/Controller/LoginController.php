<?php

declare(strict_types=1);

namespace Market\Controller;

use Market\Core\Controller;

class LoginController extends AbstractController
{
    private const PAGE_SIZE = 20;

    public function run(): void
    {
        $this->ifLoggedIn();

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

    //get Adverts and paginaiton data to display page after login
    private function getUserAdvertisementsIfExist(): void
    {
        $countUserAdverts = $this->readModel->getCountUserAdvertisements();
        if ($countUserAdverts !== 0) {
            $countOfPages = (int) ceil($countUserAdverts / self::PAGE_SIZE);

            $this->params['countOfPages'] = $countOfPages;
            $this->params['userAdverts'] = $this->readModel->getUserAdvertisements();
        }
    }

    //When user is already logged in and try to enter to login page
    private function ifLoggedIn(): void
    {
        if (isset($_SESSION['loggedin'])) {
            header('Location: /?action=userPanel&subpage=addAdv');
            exit;
        }
    }
}
