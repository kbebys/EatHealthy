<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$adverts = $params['adverts'] ?? [];
?>

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
                <a href="/?action=main$id=<?php echo $advert['id'] ?>">Sczegóły</a>
            </div>
        </div>
    <?php endfor ?>
</div>