<?php

declare(strict_types=1);

namespace Market\Controller;

class MainController extends AbstractController
{
    public int $idAdvert;

    public function run(): void
    {
        $this->setAdvertismentId();

        //If user open the advertisement
        if ($this->idAdvert) {
            $this->params['advert'] = $this->readModel->getAdvertisement($this->idAdvert);
        } else {
            $this->params['adverts'] = $this->readModel->getAdvertisements();
        }

        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function setAdvertismentId(): void
    {
        $this->idAdvert = (int) $this->request->getParam('id') ?? null;
    }
}
