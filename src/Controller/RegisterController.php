<?php

declare(strict_types=1);

namespace Market\Controller;

class RegisterController extends AbstractController
{
    public function run(): void
    {

        if ($this->request->postParam('save')) {
            $this->registerUser();
        }

        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function registerUser(): void
    {
        $registerData = [
            'login' => $this->request->postParam('username'),
            'password' => $this->request->postParam('password'),
            'pass-repeat' => $this->request->postParam('psw-repeat'),
            'email' => $this->request->postParam('email')
        ];

        if ($this->createModel->register($registerData) === true) {
            $this->page = 'login';
            $this->params['message'] = 'Rejestracja powiodła się';
        }
    }
}
