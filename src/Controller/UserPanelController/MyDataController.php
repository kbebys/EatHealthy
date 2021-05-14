<?php

declare(strict_types=1);

namespace Market\Controller\UserPanelController;

use Market\Controller\AbstractController;

class MyDataController extends AbstractController
{
    public function run(): void
    {
        $this->subpage = 'myData';

        $this->IfUserDataExist();

        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function IfUserDataExist(): void
    {
        $uData = $this->readModel->getUserData();
        //If user already added personal data
        if ($uData) {
            $this->params['uData'] = $uData;

            $this->changeUserPersonalData();
        } else {
            $this->params['listOfPlaces'] = $this->readModel->getListOfPlaces();
            if ($this->request->postParam('save')) {
                $this->addUserPersonalData();
            }
        }
    }

    private function addUserPersonalData(): void
    {
        $uData = [
            'uName' => $this->request->postParam('first-name'),
            'phone' => $this->request->postParam('phone-number'),
            'idPlace' => (int) $this->request->postParam('place')
        ];
        if ($this->createModel->addUserData($uData) === true) {
            $this->params = [
                'success' => 'Dane zostały dodane pomyślnie',
                'uData' => $this->readModel->getUserData()
            ];
        }
    }

    private function changeUserPersonalData(): void
    {
        //wich data user want to change
        switch ($this->request->getParam('change')) {
            case 'name':
                $data = $this->changeUserData('name');
                $this->params = array_merge($this->params, $data);
                break;

            case 'phone':
                $data = $this->changeUserData('phone');
                $this->params = array_merge($this->params, $data);
                break;
            case 'place':
                $this->params['listOfPlaces'] = $this->readModel->getListOfPlaces();
                $data = $this->changeUserData('place');
                $this->params = array_merge($this->params, $data);
                break;
        }
    }

    //calling appropriate function to change chosen user data and set params
    private function changeUserData(string $userData): array
    {
        $param['change'] = $userData;
        if ($this->request->postParam('save')) {
            $uData = $this->request->postParam($userData);
            $fun = 'change' . ucfirst($userData);

            if ($this->updateModel->$fun($uData) === true) {
                $param = [
                    'success' => 'Dane zostały zmienione pomyślnie',
                    'uData' => $this->readModel->getUserData(),
                    'change' => null
                ];
            }
        }
        return $param;
    }
}
