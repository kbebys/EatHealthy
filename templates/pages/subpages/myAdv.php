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

$countOfPages = $params['countOfPages'] ?? 1;
$pageNumber = $params['pageNumber'] ?? 1;

$next = ($pageNumber < $countOfPages) ? ($pageNumber + 1) : $countOfPages;
$previous = ($pageNumber > 1) ? ($pageNumber - 1) : 1;
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
                <input type="hidden" name="id-adv" value="<?php echo $userAdvert['id'] ?>">
                <input type="submit" name="save" value="Edytuj">
            </form>
            <a href="<?php echo $action ?>&advertOption=details&id=<?php echo $userAdvert['id'] ?>">Wróć</a>
        <?php break;
            //Display all of user advertisements
        case ($userAdverts): ?>

            <div class="pagination">
                <a href="<?php echo $action . '&pageNumber=' . 1 ?>">
                    <span> pierwsza </span>
                </a>
                <a href="<?php echo $action . '&pageNumber=' . $previous ?>">
                    <span> poprzednia </span>
                </a>

                <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
                    <a class="<?php echo ($i === $pageNumber) ? 'active' : '' ?>" href="<?php echo $action . '&pageNumber=' . $i ?>">
                        <span><?php echo $i ?></span>
                    </a>
                <?php endfor ?>

                <a href="<?php echo $action . '&pageNumber=' . $next ?>">
                    <span> następna </span>
                </a>
                <a href="<?php echo $action . '&pageNumber=' . $countOfPages ?>">
                    <span> ostatnia </span>
                </a>
            </div>

            <?php for ($i = 0; $i < count($userAdverts); $i++) :
                $advert = $userAdverts[$i]; ?>
                <div class="user-advert">
                    <div class="advert-data">
                        <div class="title"><?php echo $advert['title'] ?></div>
                        <div class="data">
                            <span><?php echo $advert['first_name'] ?></span>
                            <span><?php echo $advert['date'] ?></span>
                        </div>
                    </div>
                    <div class="options">
                        <a href="<?php echo $action ?>&advertOption=details&id=<?php echo $advert['id'] ?>">Sczegóły</a>
                    </div>
                </div>
            <?php endfor ?>

            <div class="pagination">
                <a href="<?php echo $action . '&pageNumber=' . 1 ?>">
                    <span> pierwsza </span>
                </a>
                <a href="<?php echo $action . '&pageNumber=' . $previous ?>">
                    <span> poprzednia </span>
                </a>

                <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
                    <a class="<?php echo ($i === $pageNumber) ? 'active' : '' ?>" href="<?php echo $action . '&pageNumber=' . $i ?>">
                        <span><?php echo $i ?></span>
                    </a>
                <?php endfor ?>

                <a href="<?php echo $action . '&pageNumber=' . $next ?>">
                    <span> następna </span>
                </a>
                <a href="<?php echo $action . '&pageNumber=' . $countOfPages ?>">
                    <span> ostatnia </span>
                </a>
            </div>

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