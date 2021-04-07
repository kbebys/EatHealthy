<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$userAdverts = $params['userAdverts'] ?? [];
$userAdvert = $params['userAdvert'] ?? [];
$kindOfAdv = $userAdvert['kind_of_transaction'] ?? 'sell';

$delete = $params['delete'] ?? [];
$editAdv = $params['edit'] ?? [];

$action = '/?action=userPanel&subpage=myAdv';
?>
<div class="user-adverts">
    <!-- Displaying content of my advertisment view. It depend what option user chose -->
    <?php switch (true):
            // Editting chosen advertisment
        case ($editAdv): ?>
            <form action="<?php echo $action ?>&advertOption=edit&id=<?php echo $userAdvert['id'] ?>" method="POST" autocomplete="off">
                <label for="title">Tytuł ogłoszenia:</label>
                <input type="text" id="title" name="title" maxlength="150" value="<?php echo $userAdvert['title'] ?>" required>
                <label for="kind">Rodzaj:</label>
                <select name="kind" id="kind" required>
                    <?php if ($kindOfAdv === 'buy') : ?>
                        <option value="sell">Sprzedam</option>
                        <option value="buy" selected value>Kupię</option>
                    <?php else : ?>
                        <option value="sell" selected value>Sprzedam</option>
                        <option value="buy">Kupię</option>
                    <?php endif ?>
                </select>
                <label for="content">Treść ogłoszenia:</label>
                <textarea name="content" id="content" cols="100" rows="5" required><?php echo $userAdvert['content'] ?></textarea>
                <label for=" place">Miejscowość</label>
                <input type="text" name="place" id="place" maxlength="100" value="<?php echo $userAdvert['place'] ?>" required>
                <input type="hidden" name="id-adv" value="<?php echo $userAdvert['id'] ?>">
                <input type="submit" name="save" value="Edytuj">
            </form>
            <a href="<?php echo $action ?>&advertOption=details&id=<?php echo $userAdvert['id'] ?>">Wróć</a>
        <?php break;
            //Display all of user advertisements
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
                        <a href="<?php echo $action ?>&advertOption=details&id=<?php echo $advert['id'] ?>">Sczegóły</a>
                    </div>
                </div>
            <?php endfor ?>
        <?php break;
            //Display one chosen advertisement with available options
        case ($userAdvert): ?>
            <div class="user-advert">
                <div class="advert-data">
                    <div class="title"><?php echo $userAdvert['title'] ?></div>
                    <div class="adv-content">
                        <?php echo $userAdvert['content'] ?>
                    </div>
                    <div class="data">
                        <span><?php echo $userAdvert['place'] ?></span>
                        <span><?php echo $userAdvert['date'] ?></span>
                    </div>
                </div>
                <div class="options">
                    <a href="<?php echo $action ?>&advertOption=edit&id=<?php echo $userAdvert['id'] ?>">Edytuj</a>
                    <a href="<?php echo $action ?>&advertOption=delete&id=<?php echo $userAdvert['id'] ?>">Usuń</a>
                    <a href="<?php echo $action ?>">Powrót do listy</a>
                </div>
            </div>
            <?php if ($delete === true) : ?>
                <div class="question">
                    <p class="message">Czy na pewno chcesz usunąć to ogłoszenie?</p>
                    <a href="<?php echo $action ?>&advertOption=delete&id=<?php echo $userAdvert['id'] ?>&question=yes">Tak</a>
                    <a href="<?php echo $action ?>&advertOption=details&id=<?php echo $userAdvert['id'] ?>&question=no">Nie</a>
                </div>
            <?php endif;
            break;

        default: ?>
            <p class="message">Nie masz ogłoszeń</p>
    <?php endswitch ?>
</div>