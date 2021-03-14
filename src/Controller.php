<?php

declare(strict_types=1);

namespace Market;

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

            if ($this->sendDatabase->login($loginData) === true) {
                $this->userPanel();
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

            if ($this->sendDatabase->register($registerData) === true) {
                $page = 'login';
                $param['message'] = 'Rejestracja powiodła się';
            }
        }
        $this->view->render($page, '', $param ?? []);
    }

    public function userPanel(): void
    {
        if ($_SESSION['loggedin']) {
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
        $subpage = $this->subpage();
        //if exist given window variable 
        if (!method_exists($this, $subpage)) {
            $subpage = self::DEFAULT_SUBPAGE;
        }
        $this->$subpage();
    }

    public function addAdv(): void
    {
        $subpage = 'addAdv';
        $this->view->render(self::DEFAULT_PAGE, $subpage);
    }

    public function myAdv(): void
    {
        $subpage = 'myAdv';
        $this->view->render(self::DEFAULT_PAGE, $subpage);
    }

    public function myData(): void
    {

        $uData = $this->getDatabase->getUserData();

        if ($uData) {
            $param['uData'] = $uData;

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
        } else {

            if ($this->request->postParam('save')) {
                $uData = [
                    'uName' => $this->request->postParam('first-name'),
                    'phone' => $this->request->postParam('phone-number')
                ];

                $param['errorWindow'] = $this->sendDatabase->sendUserData($uData);
                if ($param['errorWindow'] === 'added') {
                    $param = [
                        'errorWindow' => null,
                        'messageWindow' => 'Dane zostały dodane pomyślnie',
                        'uData' => $this->getDatabase->getUserData()
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

            $param['errorWindow'] = $this->sendDatabase->changePassword($passwords);
            if ($param['errorWindow'] === 'changed') {
                $param = [
                    'errorWindow' => null,
                    'messageWindow' => 'Hasło zostało zmienione'
                ];
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
                    $this->sendDatabase->deleteAcc();
                    $this->logout();
                    break;
                case 'nie':
                    $this->view->render(self::DEFAULT_PAGE, 'deleteAcc');
                    break;
            }

            $password = $this->request->postParam('password');

            $param['errorWindow'] = $this->getDatabase->checkPassword($password);
            if ($param['errorWindow'] === 'success') {
                $param = [
                    'errorWindow' => null,
                    'confirm' => true,
                ];
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

            $param['errorWindow'] = $this->sendDatabase->$fun($uData);
            if ($param['errorWindow'] === 'changed') {
                $param = [
                    'errorWindow' => null,
                    'messageWindow' => 'Dane zostały zmienione pomyślnie',
                    'uData' => $this->getDatabase->getUserData(),
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
