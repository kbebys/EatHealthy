<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$userAdverts = $params['userAdverts'] ?? [];

$action = '/?action=userPanel&subpage=myAdv';
?>
<div class="user-adverts">
    <?php if (!$userAdverts) : ?>
        <p class="message">Nie masz jeszcze ogłoszeń</p>
    <?php else : ?>
        <?php for ($i = 0; $i < count($userAdverts); $i++) :
            $advert = $userAdverts[$i]; ?>
            <div class="user-advert">
                <div class="advert-data">
                    <div class="title"><?php echo $advert['title'] ?></div>
                    <div class="data">
                        <span><?php echo $advert['first_name'] ?></span>
                        <span><?php echo $advert['place'] ?></span>
                        <span><?php echo $advert['date'] ?></span>
                    </div>
                </div>
                <div class="options">
                    <a href="<?php echo $action ?>&option=details&id=<?php echo $advert['id'] ?>">Sczegóły</a>
                    <a href="<?php echo $action ?>&option=delete&id=<?php echo $advert['id'] ?>">Usuń</a>
                </div>
            </div>
        <?php endfor ?>
    <?php endif ?>
</div>