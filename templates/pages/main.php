<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$adverts = $params['adverts'] ?? [];
$pageSize = $params['pageSize'] ?? 40;
$countOfPages = $params['countOfPages'] ?? 1;
$pageNumber = $params['pageNumber'] ?? 1;

$url = '/?action=main&pageSize=' . $pageSize . '&pageNumber=';

$next = ($pageNumber < $countOfPages) ? ($pageNumber + 1) : $countOfPages;
$previous = ($pageNumber > 1) ? ($pageNumber - 1) : 1;
?>

<div class="filter_menu">
    <form action="/?action=main" method="GET">
        <div class="pageSize">
            <p>Ilość wyświetlanych ogłoszeń: </p>
            <input type="radio" id="ten" name="pageSize" value="10" <?php echo $pageSize === 10 ? 'checked' : '';  ?>>
            <label for="ten"> 10</label>
            <input type="radio" id="twenty" name="pageSize" value="20" <?php echo $pageSize === 20 ? 'checked' : '';  ?>>
            <label for="twenty"> 20</label>
            <input type="radio" id="fourty" name="pageSize" value="40" <?php echo $pageSize === 40 ? 'checked' : '';  ?>>
            <label for="fourty"> 40</label>
        </div>
        <input type="submit" value="Wyślij" name="send">
    </form>
</div>

<div class="pagination">
    <a href="<?php echo $url . $previous ?>">
        <span> poprzednia </span>
    </a>

    <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
        <a href="<?php echo $url . $i ?>">
            <span><?php echo $i ?></span>
        </a>
    <?php endfor ?>

    <a href="<?php echo $url . $next ?>">
        <span> następna </span>
    </a>
</div>

<div class="advertisements">
    <!-- All of the advertisements -->
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
    <a href="<?php echo $url . $previous ?>">
        <span> poprzednia </span>
    </a>

    <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
        <a href="<?php echo $url . $i ?>">
            <span><?php echo $i ?></span>
        </a>
    <?php endfor ?>

    <a href="<?php echo $url . $next ?>">
        <span> następna </span>
    </a>
</div>