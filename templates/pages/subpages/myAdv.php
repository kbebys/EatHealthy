<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$userAdverts = $params['userAdverts'] ?? [];
?>
<div class="user-adverts">
    <?php if (!$userAdverts) : ?>
        <p class="message">Nie masz jeszcze ogłoszeń</p>
    <?php else : ?>
        <?php for ($i = 0; $i < count($userAdverts); $i++) :
            $advert = $userAdverts[$i]; ?>
            <div class="user-advert">
                <div class="title">
                    <h3><?php echo $advert['title'] ?></h3>
                </div>
                <div class="name"><?php echo $advert['first_name'] ?></div>
                <div class="phone"><?php echo $advert['phone_number'] ?></div>
                <div class="place"><?php echo $advert['place'] ?></div>
                <div class="date"><?php echo $advert['date'] ?></div>

            </div>
        <?php endfor ?>
    <?php endif ?>
</div>