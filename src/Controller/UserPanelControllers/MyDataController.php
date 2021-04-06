<?php

declare(strict_types=1);

namespace Market\Controller\UserPanelControllers;

use Market\Controller\AbstractController;
use Market\Exception\ErrorException;

class MyDataController extends AbstractController
{
    public function run(): void
    {
        $this->subpage = 'myData';
        $uData = $this->readModel->getUserData();

        //If user already added personal data
        if ($uData) {
            $this->params['uData'] = $uData;
            try {
                $this->changeUserPersonalData();
            } catch (ErrorException $e) {
                $this->params['errorWindow'] = $e->getMessage();
                $this->view->render($this->page, $this->subpage, $this->params);
                exit;
            }
        } else {

            if ($this->request->postParam('save')) {
                $this->addUserPersonalData();
            }
        }
        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function addUserPersonalData(): void
    {
        $uData = [
            'uName' => $this->request->postParam('first-name'),
            'phone' => $this->request->postParam('phone-number')
        ];;
        if ($this->createModel->addUserData($uData) === true) {
            $this->params = [
                'messageWindow' => 'Dane zostały dodane pomyślnie',
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
        }
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
}
