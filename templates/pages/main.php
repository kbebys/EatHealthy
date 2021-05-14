<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$adverts = $params['adverts'] ?? [];
$pageSize = $params['pageSize'] ?? 40;
$countOfPages = $params['countOfPages'] ?? 1;
$pageNumber = $params['pageNumber'] ?? 1;

$next = ($pageNumber < $countOfPages) ? ($pageNumber + 1) : $countOfPages;
$previous = ($pageNumber > 1) ? ($pageNumber - 1) : 1;

$searchContent = $params['searchContent'] ?? '';
$listOfPlaces = $params['listOfPlaces'] ?? [];
$idPlace = $params['idPlace'] ?? 0;
$transaction = $params['transaction'] ?? 'all';
$daysBack = $params['daysBack'] ?? 0;

$url = '/?action=main&searchContent=' . $searchContent . '&place=' . $idPlace . '&transaction=' . $transaction . '&daysBack=' . $daysBack . '&pageSize=' . $pageSize . '&pageNumber=';
?>
<section class="board-hero top-hero container-fluid mx-auto mb-5">
    <div class="hero-shadow"></div>
    <p class="text-light">Ogłoszenia</p>
</section>
<section class="board-filter">
    <div class="container">
        <div class="text-center">
            <a class="btn btn-outline-dark my-4" href="/?action=login"><i class="fas fa-plus"></i> Dodaj ogłoszenie</a>
        </div>
        <div class="menu-filter border border-dark my-5 mx-sm-n4 py-5 px-2 px-sm-5 text-center">
            <form action="/?action=main" method="GET">
                <div class="row justify-content-center">
                    <div class="search col-12 col-md-9 mb-4 mb-md-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="searchContent">Szukaj w tytule</label>
                            </div>
                            <input type="search" id="searchContent" class="form-control" name="searchContent" value="<?php echo $searchContent ?>">
                        </div>
                    </div>
                    <div class="place col-12 col-md-6 col-lg-4 mb-4 mb-md-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="place">Miejscowość</label>
                            </div>
                            <select name="place" id="place" class="custom-select">
                                <option <?php echo ($idPlace === 0) ? 'selected' : '' ?> value=""> -- Wszystkie -- </option>

                                <?php foreach ($listOfPlaces as $key) {
                                    $selectedPlace = ($key['id'] === $idPlace) ? 'selected' : '';
                                    echo '<option ' . $selectedPlace . ' value="' . $key['id'] . '">' . $key['place'] . ' (gmina: ' . $key['community'] . ')</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="type-of-transaction col-12 col-md-6 col-lg-4 mb-4 mb-md-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="transaction">Rodzaj transakcji</label>
                            </div>
                            <select name="transaction" id="transaction" class="custom-select">
                                <option <?php echo ($transaction === 'all') ? 'selected' : '' ?> value="all"> -- Wszystkie --</option>
                                <option <?php echo ($transaction === 'buy') ? 'selected' : '' ?> value="buy">Kupię</option>
                                <option <?php echo ($transaction === 'sell') ? 'selected' : '' ?> value="sell">Sprzedam</option>
                            </select>
                        </div>
                    </div>
                    <div class="last-days col-12 col-md-6 col-lg-4 mb-4 mb-md-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="last-days">Z ostatnich</label>
                            </div>
                            <select name="daysBack" id="last-days" class="custom-select">
                                <option <?php echo ($daysBack === 0) ? 'selected' : '' ?> value="">--</option>
                                <option <?php echo ($daysBack === 1) ? 'selected' : '' ?> value="1">1</option>
                                <option <?php echo ($daysBack === 2) ? 'selected' : '' ?> value="2">2</option>
                                <option <?php echo ($daysBack === 5) ? 'selected' : '' ?> value="5">5</option>
                                <option <?php echo ($daysBack === 10) ? 'selected' : '' ?> value="10">10</option>
                                <option <?php echo ($daysBack === 20) ? 'selected' : '' ?> value="20">20</option>
                                <option <?php echo ($daysBack === 30) ? 'selected' : '' ?> value="30">30</option>
                            </select>
                            <div class="input-group-append">
                                <label class="input-group-text" for="last-days">dni</label>
                            </div>
                        </div>
                    </div>
                    <div class="page-size col-9 mb-4 mb-md-5">
                        <p class="m-0">Ilość wyświetlanych ogłoszeń na stronie: </p>
                        <div class="form-check-inline">
                            <input type="radio" id="ten" class="form-check-input" name="pageSize" value="10" <?php echo $pageSize === 10 ? 'checked' : '';  ?>>
                            <label class="form-check-label" for="ten"> 10</label>
                        </div>
                        <div class="form-check-inline">
                            <input type="radio" id="twenty" class="form-check-input" name="pageSize" value="20" <?php echo $pageSize === 20 ? 'checked' : '';  ?>>
                            <label class="form-check-label" for="twenty"> 20</label>
                        </div>
                        <div class="form-check-inline">
                            <input type="radio" id="fourty" class="form-check-input" name="pageSize" value="40" <?php echo $pageSize === 40 ? 'checked' : '';  ?>>
                            <label class="form-check-label" for="fourty"> 40</label>
                        </div>
                    </div>
                    <div class="col-9">
                        <input class="btn btn-dark" type="submit" value="Szukaj" name="search">
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php if ($countOfPages !== 0) : ?>
    <nav class="board-pagination pt-5">
        <div class="container">
            <ul class="pagination d-flex justify-content-center">
                <?php if ($pageNumber !== 1) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $url . 1 ?>"><i class="fas fa-angle-double-left"></i></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $url . $previous ?>"><i class="fas fa-angle-left"></i></a>
                    </li>
                <?php endif ?>

                <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
                    <?php if ($i < ($pageNumber - 2) || $i > ($pageNumber + 2)) : ?>
                    <?php else : ?>
                        <li class="page-item <?php echo ($i === $pageNumber) ? 'active' : '' ?>">
                            <a class="page-link" href="<?php echo $url . $i ?>"><?php echo $i ?></a>
                        </li>
                    <?php endif ?>
                <?php endfor ?>

                <?php if ($pageNumber !== $countOfPages) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $url . $next ?>"><i class="fas fa-angle-right"></i></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $url . $countOfPages ?>"><i class="fas fa-angle-double-right"></i></a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </nav>
    <section class="board-adverts destination">
        <div class="container">
            <?php for ($i = 0; $i < count($adverts); $i++) :
                $advert = $adverts[$i]; ?>
                <div class="jumbotron jumbotron-fluid pb-4 my-5">
                    <div class="advert px-2 px-sm-5">
                        <div class="title pb-4">
                            <a href="/?action=main&id=<?php echo $advert['id'] ?>">
                                <p class="h2 text-dark"><?php echo $advert['title'] ?></p>
                            </a>
                        </div>
                        <hr class="my-sm-4">
                        <div class="advert-data d-flex flex-wrap justify-content-between text-muted pt-4">
                            <p class="mb-0 mr-3"><i class="fas fa-map-marker-alt"></i> <?php echo $advert['place'] ?></p>
                            <p><i class="fas fa-calendar-alt"></i> <?php echo $advert['date'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </section>
    <nav class="board-pagination pb-5">
        <div class="container">
            <ul class="pagination d-flex justify-content-center">
                <?php if ($pageNumber !== 1) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $url . 1 ?>"><i class="fas fa-angle-double-left"></i></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $url . $previous ?>"><i class="fas fa-angle-left"></i></a>
                    </li>
                <?php endif ?>

                <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
                    <?php if ($i < ($pageNumber - 2) || $i > ($pageNumber + 2)) : ?>
                    <?php else : ?>
                        <li class="page-item <?php echo ($i === $pageNumber) ? 'active' : '' ?>">
                            <a class="page-link" href="<?php echo $url . $i ?>"><?php echo $i ?></a>
                        </li>
                    <?php endif ?>
                <?php endfor ?>

                <?php if ($pageNumber !== $countOfPages) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $url . $next ?>"><i class="fas fa-angle-right"></i></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $url . $countOfPages ?>"><i class="fas fa-angle-double-right"></i></a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </nav>
<?php else : ?>
    <section class="board-empty pt-5">
        <div class="container">
            <p class="alert alert-danger">Nie znaleziono ogłoszeń!!</p>
        </div>
    </section>
<?php endif ?>