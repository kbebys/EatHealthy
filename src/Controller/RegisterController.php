<?php

declare(strict_types=1);

namespace Market\Controller;

use Exception;

class RegisterController extends AbstractController
{
    public function run(): void
    {
        $this->checkRecaptchaSecretKey();

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
            'email' => $this->request->postParam('email'),
            'recaptcha' => $this->request->postParam('g-recaptcha-response')
        ];

        if ($this->createModel->register($registerData, self::$configuration['recaptcha']) === true) {
            $this->page = 'login';
            $this->params['success'] = 'Rejestracja powiodła się';
        }
    }

    private function checkRecaptchaSecretKey(): void
    {
        if (empty(self::$configuration['recaptcha'])) {
            throw new Exception("Błąd konfiguracji systemu rejestracji. Spróbuj później.");
        }
    }
}
