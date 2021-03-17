<?php

declare(strict_types=1);

namespace Market\Controller;

use Market\Controller\AbstractController;
use Market\Exception\ErrorException;

session_start();

//class uses to control what content will display
class Controller extends AbstractController
{
    private const DEFAULT_PAGE = 'userPanel';
    private const DEFAULT_SUBPAGE = 'addAdv';

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
                $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_SUBPAGE);
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
        $loggedin = $_SESSION['loggedin'] ?? '';
        if ($loggedin) {
            $this->runWindow();
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

    //Functions use to Control userPanel window

    public function runWindow(): void
    {
        try {
            $subpage = $this->subpage();
            //if exist given window variable 
            if (!method_exists($this, $subpage)) {
                $subpage = self::DEFAULT_SUBPAGE;
            }
            $this->$subpage();
        } catch (ErrorException $e) {
            $param['errorWindow'] = $e->getMessage();
            $this->view->render(self::DEFAULT_PAGE, $subpage, $param ?? []);
        }
    }

    public function addAdv(): void
    {
        $subpage = 'addAdv';

        if (!$this->readModel->getUserData()) {
            $this->view->render(self::DEFAULT_PAGE, 'myData');
        }
        dump($this->request->postParam('save'));
        if ($this->request->postParam('save')) {
            $advData = [
                'title' => $this->request->postParam('title'),
                'kind' => $this->request->postParam('kind'),
                'content' => $this->request->postParam('content'),
                'place' => $this->request->postParam('place')
            ];

            if ($this->createModel->addAdvertisment($advData)) {
                $param['messageWindow'] = 'Dodałeś ogłoszenie';
                $this->view->render(self::DEFAULT_PAGE, 'myAdv', $param);
            }
        }

        $this->view->render(self::DEFAULT_PAGE, $subpage);
    }

    public function myAdv(): void
    {
        $subpage = 'myAdv';
        $this->view->render(self::DEFAULT_PAGE, $subpage);
    }

    public function myData(): void
    {

        $uData = $this->readModel->getUserData();

        if ($uData) {
            $param['uData'] = $uData;
            try {
                switch ($this->request->getParam('change')) {
                    case 'name':
                        $data = $this->changeUserData('name');
                        $param = array_merge($param, $data);
                        break;

                    case 'phone':
                        $data = $this->changeUserData('phone');
                        $param = array_merge($param, $data);
                        break;
                }
            } catch (ErrorException $e) {
                $param['errorWindow'] = $e->getMessage();
                $this->view->render(self::DEFAULT_PAGE, 'myData', $param ?? []);
            }
        } else {

            if ($this->request->postParam('save')) {
                $uData = [
                    'uName' => $this->request->postParam('first-name'),
                    'phone' => $this->request->postParam('phone-number')
                ];;
                if ($this->createModel->sendUserData($uData) === true) {
                    $param = [
                        'messageWindow' => 'Dane zostały dodane pomyślnie',
                        'uData' => $this->readModel->getUserData()
                    ];
                }
            }
        }
        $this->view->render(self::DEFAULT_PAGE, 'myData', $param ?? []);
    }

    public function changePass(): void
    {
        if ($this->request->postParam('save')) {
            $passwords = [
                'old' => $this->request->postParam('password'),
                'new' => $this->request->postParam('new-password'),
                'newRepeat' => $this->request->postParam('new-repeat-password'),
            ];


            if ($this->updateModel->changePassword($passwords) === true) {
                $param['messageWindow'] = 'Hasło zostało zmienione';
            }
        }

        $this->view->render(self::DEFAULT_PAGE, 'changePass', $param ?? []);
    }

    public function deleteAcc(): void
    {
        $save = $this->request->postParam('save');

        if ($save) {
            switch ($save) {
                case 'tak':
                    $this->deleteModel->deleteAcc();
                    $this->logout();
                    break;
                case 'nie':
                    $this->view->render(self::DEFAULT_PAGE, 'deleteAcc');
                    break;
                case 'usuń':
                    $password = $this->request->postParam('password');
                    if ($this->readModel->checkPassword($password) === true) {
                        $param['confirm'] = true;
                    }
                    break;
            }
        }
        $this->view->render(self::DEFAULT_PAGE, 'deleteAcc', $param ?? []);
    }

    private function changeUserData(string $data): array
    {
        $param['change'] = $data;
        if ($this->request->postParam('save')) {
            $uData = $this->request->postParam($data);
            $fun = 'change' . ucfirst($data);

            if ($this->updateModel->$fun($uData) === true) {
                $param = [
                    'messageWindow' => 'Dane zostały zmienione pomyślnie',
                    'uData' => $this->readModel->getUserData(),
                    'change' => null
                ];
            }
        }
        return $param;
    }

    private function subpage(): string
    {
        return $this->request->getParam('subpage', self::DEFAULT_SUBPAGE);
    }
}
