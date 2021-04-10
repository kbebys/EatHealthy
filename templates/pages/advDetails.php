<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$advert = $params['advert'] ?? [];
?>

<div class="advertisements">
    <!-- One chosen an advertisement with details -->
    <div class="advert">
        <div class="advert-data">
            <div class="title"><?php echo $advert['title'] ?></div>
            <div class="adv-content">
                <?php echo $advert['content'] ?>
            </div>
            <div class="data">
                <span><?php echo $advert['place'] ?></span>
                <span><?php echo $advert['date'] ?></span>
                <span><?php echo $advert['first_name'] ?></span>
                <span>Nr. tel.: <?php echo $advert['phone_number'] ?></span>
            </div>
        </div>
        <div class="options">
            <a href="javascript:history.go(-1)">Powrót</a>
        </div>
    </div>
</div>