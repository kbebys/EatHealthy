<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$adverts = $params['adverts'] ?? [];
$advert = $params['advert'] ?? [];
?>
<div class="advertisements">
    <?php if ($adverts) : ?>
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
        <?php endfor;
    elseif ($advert) : ?>
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
                <a href="/?action=main">Powrót</a>
            </div>
        </div>
    <?php endif ?>
</div>