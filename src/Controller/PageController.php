<?php

declare(strict_types=1);

namespace Market\Controller;

use Market\Controller\AbstractController;
use Market\Exception\ErrorException;

//class uses to control what content will display
class PageController extends AbstractController
{
    public function main(): void
    {
        $param['adverts'] = $this->readModel->getAdvertisments();
        $this->view->render('main', '', $param);
    }

    public function login(): void
    {
        $page = 'login';

        if ($this->request->postParam('save')) {

            $loginData = [
                'login' => $this->request->postParam('username'),
                'password' => $this->request->postParam('password')
            ];

            if ($this->readModel->login($loginData) === true) {
                $page = 'userPanel';
                $subpage = 'myAdv';

                if ($this->readModel->getCountUserAdvertisments() !== 0) {
                    $param['userAdverts'] = $this->readModel->getUserAdvertisments();
                }
            }
        }
        $this->view->render($page, $subpage ?? '', $param ?? []);
    }


    public function register(): void
    {
        $page = 'register';

        if ($this->request->postParam('save')) {

            $registerData = [
                'login' => $this->request->postParam('username'),
                'password' => $this->request->postParam('password'),
                'pass-repeat' => $this->request->postParam('psw-repeat'),
                'email' => $this->request->postParam('email')
            ];

            if ($this->createModel->register($registerData) === true) {
                $page = 'login';
                $param['message'] = 'Rejestracja powiodła się';
            }
        }
        $this->view->render($page, '', $param ?? []);
    }

    public function userPanel(): void
    {
        //Check if user is logged
        $loggedin = $_SESSION['loggedin'] ?? '';
        if ($loggedin) {
            $action = 'UserPanelRun';
            $this->$action();
        } else {
            $this->view->render('main');
        }
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: /?action=main");
        exit;
    }
}
