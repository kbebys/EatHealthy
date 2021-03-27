<?php

declare(strict_types=1);

namespace Market\Controller;

use Market\Exception\ErrorException;

//The class handles user panel window
class UserPanelController extends AbstractController
{
    private const DEFAULT_SUBPAGE = 'addAdv';
    private const DEFAULT_PAGE = 'userPanel';
    private const DEFAULT_USER_ADVERT = 'myAdv';

    //Which function will be called
    public function userPanelRun(): void
    {
        try {
            $subpage = $this->subpage();

            if (!method_exists($this, $subpage)) {
                $subpage = self::DEFAULT_SUBPAGE;
            }
            $this->$subpage();
        } catch (ErrorException $e) {
            //Handle Errors throwing during exchange data between database and page
            $param['errorWindow'] = $e->getMessage();
            $this->view->render(self::DEFAULT_PAGE, $subpage, $param ?? []);
        }
    }

    //Add new advertisement
    public function addAdv(): void
    {
        //If user did not add its personal data
        if (!$this->readModel->getUserData()) {
            $this->view->render(self::DEFAULT_PAGE, 'myData');
        }

        $savePost = $this->request->postParam('save');
        if ($savePost && $savePost !== 'Zaloguj') {
            $advData = [
                'title' => $this->request->postParam('title'),
                'kind' => $this->request->postParam('kind'),
                'content' => $this->request->postParam('content'),
                'place' => $this->request->postParam('place')
            ];

            if ($this->createModel->addAdvertisment($advData) === true) {
                $param['messageWindow'] = 'Dodałeś ogłoszenie';
            }
        }
        $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_SUBPAGE, $param ?? []);
    }

    //Method to Controll User Advertisments supbage in User Panel
    public function myAdv(): void
    {
        try {

            $advertOption = $this->userAdvertOption();
            $advertOption = $advertOption . 'UserAdvert';

            $idAdv = (int) $this->request->getParam('id');

            dump($advertOption);

            if (!method_exists($this, $advertOption)) {
                //Get all of the User advertisments
                $param['userAdverts'] = $this->readModel->getUserAdvertisments();
                $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
            } else {
                $this->$advertOption($idAdv);
            }
        } catch (ErrorException $e) {
            $param['errorWindow'] = $e->getMessage();

            //Code === 2 when error about editing advertisement is throwing
            if ($e->getCode() === 2) {
                $param['userAdvert'] = $this->readModel->getUserAdvertisment($idAdv);
            } else {
                $param['userAdverts'] = $this->readModel->getUserAdvertisments();
            }

            $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
        }
    }

    public function myData(): void
    {
        $uData = $this->readModel->getUserData();

        if ($uData) {

            if (isset($uData['error'])) {
                $param['errorWindow'] = 'Problem z pobraniem danych';
            }

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
                    case ($uData[0] = 'error'):
                        $param['errorWindow'] = 'Problem z pobraniem danych';
                        break;
                }
            } catch (ErrorException $e) {
                $param['errorWindow'] = $e->getMessage();
                $this->view->render(self::DEFAULT_PAGE, 'myData', $param ?? []);
                exit;
            }
        } else {

            if ($this->request->postParam('save')) {
                $uData = [
                    'uName' => $this->request->postParam('first-name'),
                    'phone' => $this->request->postParam('phone-number')
                ];;
                if ($this->createModel->addUserData($uData) === true) {
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
                    // $this->logout();
                    $this->view->render('logout');
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

    //display details of chosen advertisement
    private function detailsUserAdvert(int $idAdv): void
    {
        $param['userAdvert'] = $this->readModel->getUserAdvertisment($idAdv);
        $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
    }

    private function editUserAdvert(int $idAdv): void
    {
        $param['userAdvert'] = $this->readModel->getUserAdvertisment($idAdv);

        //It is flag uses to display editing view
        $param['edit'] = true;

        if ($this->request->postParam('save')) {
            $advData = [
                'title' => $this->request->postParam('title'),
                'kind' => $this->request->postParam('kind'),
                'content' => $this->request->postParam('content'),
                'place' => $this->request->postParam('place'),
            ];

            if ($this->updateModel->changeAdvertisment($advData, $idAdv) === true) {
                $param['edit'] = null;
                $param['messageWindow'] = 'Twoje ogłoszenie zostało zmienione';
                $param['userAdvert'] = $this->readModel->getUserAdvertisment($idAdv);
            }
        }
        $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
    }

    private function deleteUserAdvert(int $idAdv): void
    {
        //Confirmation if User really wants to delete advertisement
        $ifDelete = $this->request->getParam('question');

        $param['userAdvert'] = $this->readModel->getUserAdvertisment($idAdv);

        if ($ifDelete === 'yes') {
            if ($this->deleteModel->deleteUserAdvertisment($idAdv) === true) {
                $param = [
                    'messageWindow' => 'Ogłoszenie zostało usunięte',
                    'userAdverts' => $this->readModel->getUserAdvertisments()
                ];
            }
        } elseif ($ifDelete === 'no') {
            //Situation when user resign from deleting 
            $param['delete'] = false;
        } else {
            //situation before confirmation. When User clicked delete
            $param['delete'] = true;
        }
        $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
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

    //Get what option chose User in my advertisments subpage
    private function userAdvertOption(): string
    {
        return $this->request->getParam('advertOption', '');
    }

    //Get information wich subpage chose user
    private function subpage(): string
    {
        return $this->request->getParam('subpage', self::DEFAULT_SUBPAGE);
    }
}
