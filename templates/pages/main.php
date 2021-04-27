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

<div class="filter-menu">
    <form action="/?action=main" method="GET">

        <div class="search">
            <label for="searchContent">Szukaj w tytule: </label>
            <input type="search" id="searchContent" name="searchContent" value="<?php echo $searchContent ?>">
        </div>

        <div class="place">
            <label for="place">Nazwa miejscowości:</label>
            <select name="place" id="place">
                <option <?php echo ($idPlace === 0) ? 'selected' : '' ?> value=""> -- Wszystkie miejscowości -- </option>

                <?php foreach ($listOfPlaces as $key) {
                    $selectedPlace = ($key['id'] === $idPlace) ? 'selected' : '';
                    echo '<option ' . $selectedPlace . ' value="' . $key['id'] . '">' . $key['place'] . ' (gmina: ' . $key['community'] . ')</option>';
                } ?>
            </select>
        </div>

        <div class="type-of-transaction">
            <label for="transaction">Rodzaj transakcji:</label>
            <select name="transaction" id="transaction">
                <option <?php echo ($transaction === 'all') ? 'selected' : '' ?> value="all"> -- Wszystkie --</option>
                <option <?php echo ($transaction === 'buy') ? 'selected' : '' ?> value="buy">Kupię</option>
                <option <?php echo ($transaction === 'sell') ? 'selected' : '' ?> value="sell">Sprzedam</option>
            </select>
        </div>

        <div class="last-days">
            <label for="last-days">Z ostatnich dni:</label>
            <select name="daysBack" id="last-days">
                <option <?php echo ($daysBack === 0) ? 'selected' : '' ?> value="">--</option>
                <option <?php echo ($daysBack === 1) ? 'selected' : '' ?> value="1">1</option>
                <option <?php echo ($daysBack === 2) ? 'selected' : '' ?> value="2">2</option>
                <option <?php echo ($daysBack === 5) ? 'selected' : '' ?> value="5">5</option>
                <option <?php echo ($daysBack === 10) ? 'selected' : '' ?> value="10">10</option>
                <option <?php echo ($daysBack === 20) ? 'selected' : '' ?> value="20">20</option>
                <option <?php echo ($daysBack === 30) ? 'selected' : '' ?> value="30">30</option>
            </select>
        </div>

        <div class="page-size">
            <p>Ilość wyświetlanych ogłoszeń na stronie: </p>
            <input type="radio" id="ten" name="pageSize" value="10" <?php echo $pageSize === 10 ? 'checked' : '';  ?>>
            <label for="ten"> 10</label>
            <input type="radio" id="twenty" name="pageSize" value="20" <?php echo $pageSize === 20 ? 'checked' : '';  ?>>
            <label for="twenty"> 20</label>
            <input type="radio" id="fourty" name="pageSize" value="40" <?php echo $pageSize === 40 ? 'checked' : '';  ?>>
            <label for="fourty"> 40</label>
        </div>
        <input type="submit" value="Szukaj" name="search">
    </form>
</div>

<?php if ($countOfPages !== 0) : ?>
    <div class="pagination">
        <?php if ($pageNumber !== 1) : ?>
            <a href="<?php echo $url . 1 ?>">
                <span> pierwsza </span>
            </a>
            <a href="<?php echo $url . $previous ?>">
                <span> poprzednia </span>
            </a>
        <?php endif ?>

        <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
            <?php if ($i < ($pageNumber - 2) || $i > ($pageNumber + 2)) : ?>
            <?php else : ?>
                <a class="<?php echo ($i === $pageNumber) ? 'active' : '' ?>" href="<?php echo $url . $i ?>">
                    <span><?php echo $i ?></span>
                </a>
            <?php endif ?>
        <?php endfor ?>

        <?php if ($pageNumber !== $countOfPages) : ?>
            <a href="<?php echo $url . $next ?>">
                <span> następna </span>
            </a>
            <a href="<?php echo $url . $countOfPages ?>">
                <span> ostatnia </span>
            </a>
        <?php endif ?>
    </div>

    <div class="advertisements">
        <?php for ($i = 0; $i < count($adverts); $i++) :
            $advert = $adverts[$i]; ?>
            <div class="advert">
                <div class="advert-data">
                    <div class="title"><?php echo $advert['title'] ?></div>
                    <div class="data">
                        <span><?php echo $advert['place'] ?></span>
                        <span><?php echo $advert['date'] ?></span>
                    </div>
                </div>
                <div class="options">
                    <a href="/?action=main&id=<?php echo $advert['id'] ?>">Sczegóły</a>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <div class="pagination">
        <?php if ($pageNumber !== 1) : ?>
            <a href="<?php echo $url . 1 ?>">
                <span> pierwsza </span>
            </a>
            <a href="<?php echo $url . $previous ?>">
                <span> poprzednia </span>
            </a>
        <?php endif ?>

        <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
            <?php if ($i < ($pageNumber - 2) || $i > ($pageNumber + 2)) : ?>
            <?php else : ?>
                <a class="<?php echo ($i === $pageNumber) ? 'active' : '' ?>" href="<?php echo $url . $i ?>">
                    <span><?php echo $i ?></span>
                </a>
            <?php endif ?>
        <?php endfor ?>

        <?php if ($pageNumber !== $countOfPages) : ?>
            <a href="<?php echo $url . $next ?>">
                <span> następna </span>
            </a>
            <a href="<?php echo $url . $countOfPages ?>">
                <span> ostatnia </span>
            </a>
        <?php endif ?>
    </div>
<?php else : ?>
    <p class="message">Nie znaleziono ogłoszeń</p>
<?php endif ?>