<?php

declare(strict_types=1);

namespace Market\Controller;

use Market\Exception\ErrorException;

//The class handles user panel window
class UserPanelController extends PageController
{
    private const DEFAULT_PAGE = 'userPanel';
    private const DEFAULT_SUBPAGE = 'myAdv';
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
        $subpage = 'addAdv';
        //If user did not add its personal data
        if (!$this->readModel->getUserData()) {
            $subpage = 'myData';
        }

        $savePost = $this->request->postParam('save');
        if ($savePost) {
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
        $this->view->render(self::DEFAULT_PAGE, $subpage, $param ?? []);
    }

    //Method to Controll User Advertisments supbage in User Panel
    public function myAdv(): void
    {
        try {

            $advertOption = $this->userAdvertOption();
            $advertOption = $advertOption . 'UserAdvert';

            $idAdv = (int) $this->request->getParam('id');

            if (!method_exists($this, $advertOption)) {
                $advertOption = 'userAdvert';
            }
            $this->$advertOption($idAdv);
        } catch (ErrorException $e) {
            $param['errorWindow'] = $e->getMessage();

            //Code === 2 when error about editing advertisement is throwing
            if ($e->getCode() === 2) {
                $param['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
            } else {
                $param['userAdverts'] = $this->readModel->getUserAdvertisements();
            }

            $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
        }
    }

    //Controll adding and changing user data
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

    private function userAdvert($idAdv): void
    {
        $countOfAdverts = $this->readModel->getCountUserAdvertisements();
        //when user doesn't have adverts don't get them from database
        if ($countOfAdverts !== 0) {
            $param['userAdverts'] = $this->readModel->getUserAdvertisements();
        }

        $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
    }

    //display details of chosen advertisement
    private function detailsUserAdvert(int $idAdv): void
    {
        $param['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
        $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
    }

    private function editUserAdvert(int $idAdv): void
    {
        $param['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);

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
                $param['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);
            }
        }
        $this->view->render(self::DEFAULT_PAGE, self::DEFAULT_USER_ADVERT, $param ?? []);
    }

    private function deleteUserAdvert(int $idAdv): void
    {
        //Confirmation if User really wants to delete advertisement
        $ifDelete = $this->request->getParam('question');

        $param['userAdvert'] = $this->readModel->getUserAdvertisement($idAdv);

        if ($ifDelete === 'yes') {
            if ($this->deleteModel->deleteUserAdvertisment($idAdv) === true) {
                $param = [
                    'messageWindow' => 'Ogłoszenie zostało usunięte',
                    'userAdverts' => $this->readModel->getUserAdvertisements()
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
