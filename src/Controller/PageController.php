<?php

declare(strict_types=1);

namespace Market\Controller;

use Market\Controller\AbstractController;
use Market\Exception\ErrorException;

session_start();

//class uses to control what content will display
class PageController extends AbstractController
{
    public function main(): void
    {
        $this->view->render('main');
    }

    public function login(): void
    {
        if ($this->request->postParam('save')) {

            $loginData = [
                'login' => $this->request->postParam('username'),
                'password' => $this->request->postParam('password')
            ];

            if ($this->readModel->login($loginData) === true) {
                $this->userPanel();
                exit;
            }
        }
        $this->view->render('login', '', $param ?? []);
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
            (new UserPanelController($this->request))->userPanelRun();
            // $this->UserPanelRun();
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
