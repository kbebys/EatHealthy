<?php

declare(strict_types=1);

namespace Market\Controller;

class MainController extends AbstractController
{
    public int $idAdvert;

    private const PAGE_SIZE = 40;
    private const PAGE_SIZES = [10, 20, 40];

    public function run(): void
    {
        $this->setAdvertisementId();

        $this->handleAdvertisements();

        $this->view->render($this->page, $this->subpage, $this->params);
    }

    private function handleAdvertisements(): void
    {
        //If user open the advertisement
        if ($this->idAdvert) {
            $this->page = 'advDetails';
            $this->params['advert'] = $this->readModel->getAdvertisement($this->idAdvert);
        } else {
            $this->displayingAdvertisements();
        }
    }

    private function displayingAdvertisements(): void
    {
        $this->params['places'] = $this->getPlace();
        $searchContent = $this->request->getParam('searchContent', '');

        if ($searchContent) {
            $countOfAdvs = $this->readModel->getCountAdvertisements($searchContent);
        } else {
            $countOfAdvs = $this->readModel->getCountAdvertisements();
        }

        $pageSize = $this->getPageSize();
        $countOfPages = (int) ceil($countOfAdvs / $pageSize);
        $pageNumber = $this->getPageNumber() > $countOfPages ? 1 : $this->getPageNumber();

        $this->params['searchContent'] = $searchContent;
        $this->params['pageSize'] = $pageSize;
        $this->params['pageNumber'] = $pageNumber;
        $this->params['countOfPages'] = $countOfPages;
        $this->params['adverts'] = $this->readModel->getAdvertisements($pageNumber, $pageSize, $searchContent);
    }


    //Get how many advertisemets is display
    private function getPageSize(): int
    {
        $number = (int) $this->request->getParam('pageSize', self::PAGE_SIZE);

        if (!in_array($number, self::PAGE_SIZES, true)) {
            $number = self::PAGE_SIZE;
        }

        return $number;
    }

    //Get the places from Advs
    private function getPlace(): array
    {
        $places = $this->readModel->getPlaces();
        dump($places);
        return $places;
    }

    //Get wich page with advertisements is display
    private function getPageNumber(): int
    {
        return (int) $this->request->getParam('pageNumber', 1);
    }

    private function setAdvertisementId(): void
    {
        $this->idAdvert = (int) $this->request->getParam('id');
    }
}
