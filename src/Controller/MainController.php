<?php

declare(strict_types=1);

namespace Market\Controller;

class MainController extends AbstractController
{
    public int $idAdvert;

    private const PAGE_SIZES = [10, 20, 40];
    private const TYPES_OF_TRANSACTIIONS = ['all', 'buy', 'sell'];
    private const DAYS_BACK = [1, 2, 5, 10, 20, 30];

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
            $this->setDisplayingAdvertisements();
        }
    }

    private function setDisplayingAdvertisements(): void
    {
        $this->setFilterParams();

        $this->params['adverts'] = $this->readModel->getAdvertisements(
            $this->params['pageNumber'],
            $this->params['pageSize'],
            $this->params['searchContent'],
            $this->params['idPlace'],
            $this->params['transaction'],
            $this->params['daysBack']
        );
    }

    //Try to keep order setting of params because change it may ruin the logic
    private function setFilterParams(): void
    {
        $this->params['listOfPlaces'] = $this->readModel->getListOfPlaces();
        $this->params['searchContent'] = $this->request->getParam('searchContent', '');
        $this->params['idPlace'] = (int) $this->request->getParam('place', 0);
        $this->setTypeOftransaction();
        $this->setCountOfDaysBack();

        $countOfAdvs = $this->getCountofAdvs();

        $this->setPageSize();
        $this->params['countOfPages'] = (int) ceil($countOfAdvs / $this->params['pageSize']);
        $this->setPageNumber();
    }

    private function getCountofAdvs(): int
    {
        $countOfAdvs = $this->readModel->getCountAdvertisements(
            $this->params['searchContent'],
            $this->params['idPlace'],
            $this->params['transaction'],
            $this->params['daysBack']
        );

        return $countOfAdvs;
    }

    private function setCountOfDaysBack(): void
    {
        $daysBack = (int) $this->request->getParam('daysBack', 0);

        //check if count of daysBack is available
        if (!in_array($daysBack, self::DAYS_BACK, true)) {
            $daysBack = 0;
        }

        $this->params['daysBack'] =  $daysBack;
    }

    private function setTypeOftransaction(): void
    {
        $transaction = $this->request->getParam('transaction', 'all');
        //check if got transaction is equal to available options
        if (!in_array($transaction, self::TYPES_OF_TRANSACTIIONS, true)) {
            $transaction = self::TYPES_OF_TRANSACTIIONS[0];
        }

        $this->params['transaction'] = $transaction;
    }


    //Get how many advertisemets is display on one page
    private function setPageSize(): void
    {
        $number = (int) $this->request->getParam('pageSize', self::PAGE_SIZES[2]);

        //Check if page size has one of default values
        if (!in_array($number, self::PAGE_SIZES, true)) {
            $number = self::PAGE_SIZES[2];
        }

        $this->params['pageSize'] = $number;
    }

    //Get wich page with advertisements is display
    private function setPageNumber(): void
    {
        $pageNumber = (int) $this->request->getParam('pageNumber', 1);

        $pageNumber = (($pageNumber > $this->params['countOfPages'])  || ($pageNumber < 1)) ? 1 : $pageNumber;

        $this->params['pageNumber'] = $pageNumber;
    }

    private function setAdvertisementId(): void
    {
        $this->idAdvert = (int) $this->request->getParam('id');
    }
}
