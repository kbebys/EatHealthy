<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$userAdverts = $params['userAdverts'] ?? [];
$userAdvert = $params['userAdvert'] ?? [];
$delete = $params['delete'] ?? [];

$action = '/?action=userPanel&subpage=myAdv';
?>
<div class="user-adverts">
    <?php switch (true):
        case ($userAdverts): ?>
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
                    </div>
                </div>
            <?php endfor ?>
        <?php break;
        case ($userAdvert): ?>
            <div class="user-advert">
                <div class="advert-data">
                    <div class="title"><?php echo $userAdvert['title'] ?></div>
                    <div class="adv-content"><?php echo $userAdvert['content'] ?></div>
                    <div class="data">
                        <span><?php echo $userAdvert['place'] ?></span>
                        <span><?php echo $userAdvert['date'] ?></span>
                    </div>
                </div>
                <div class="options">
                    <a href="<?php echo $action ?>&option=edit&id=<?php echo $userAdvert['id'] ?>">Edytuj</a>
                    <a href="<?php echo $action ?>&option=ifDelete&id=<?php echo $userAdvert['id'] ?>">Usuń</a>
                    <a href="<?php echo $action ?>">Powrót do listy</a>
                </div>
            </div>
            <?php if ($delete === true) : ?>
                <div class="question">
                    <p class="message">Czy na pewno chcesz usunąć to ogłoszenie?</p>
                    <a href="<?php echo $action ?>&option=delete&id=<?php echo $userAdvert['id'] ?>">Tak</a>
                    <a href="<?php echo $action ?>&option=details&id=<?php echo $userAdvert['id'] ?>">Nie</a>
                </div>
            <?php endif;
            break;
        default: ?>
            <p class="message">Nie masz jeszcze ogłoszeń</p>
    <?php endswitch ?>
</div>