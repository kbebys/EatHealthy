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
<section class="user-adverts">
    <div class="container text-center">
        <div class="user-title flex-column flex-md-row text-dark py-3 py-md-5">
            <i class="fas fa-table px-4"></i>
            <h2>Moje ogłoszenia</h2>
        </div>
        <div class="destination"></div>
        <?php switch (true):
                // Editting chosen advertisment
            case ($editAdv): ?>
                <div class="edit-advert">
                    <div class="row text-center justify-content-center mb-3">
                        <div class="col-md-10 border border-dark m-4 py-5">
                            <div class="px-2 px-sm-5">
                                <form action="<?php echo $action ?>&advertOption=edit&id=<?php echo $userAdvert['id'] ?>" method="POST" autocomplete="off">
                                    <div class="form-group mb-4">
                                        <label for="title">Tytuł ogłoszenia:</label>
                                        <input type="text" id="title" class="form-control" name="title" maxlength="150" value="<?php echo $userAdvert['title'] ?>" required>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="kind">Rodzaj:</label>
                                        <select name="kind" id="kind" class="form-control" required>
                                            <?php if ($kindOfAdv === 'buy') : ?>
                                                <option value="sell">Sprzedam</option>
                                                <option value="buy" selected value>Kupię</option>
                                            <?php else : ?>
                                                <option value="sell" selected value>Sprzedam</option>
                                                <option value="buy">Kupię</option>
                                            <?php endif ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="content">Treść ogłoszenia:</label>
                                        <textarea name="content" id="content" class="form-control" rows="8" required><?php echo $userAdvert['content'] ?></textarea>
                                    </div>
                                    <input type="hidden" name="id-adv" value="<?php echo $userAdvert['id'] ?>">
                                    <input class="btn btn-dark" type="submit" name="save" value="Edytuj">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <a class="btn btn-outline-dark" href="<?php echo $action ?>&advertOption=details&id=<?php echo $userAdvert['id'] ?>"><i class="fas fa-long-arrow-alt-left"></i> Powrót</a>
                    </div>
                </div>
            <?php break;
                //Display all of user advertisements
            case ($userAdverts): ?>
                <nav class="pagination pt-5">
                    <ul class="pagination w-100 d-flex justify-content-center">
                        <?php if ($pageNumber !== 1) : ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . 1 ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . $previous ?>">
                                    <i class="fas fa-angle-left"></i></a>
                                </a>
                            </li>
                        <?php endif ?>

                        <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
                            <li class="page-item <?php echo ($i === $pageNumber) ? 'active' : '' ?>">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . $i ?>">
                                    <span><?php echo $i ?></span>
                                </a>
                            </li>
                        <?php endfor ?>

                        <?php if ($pageNumber !== $countOfPages) : ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . $next ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . $countOfPages ?>">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        <?php endif ?>
                    </ul>
                </nav>
                <section class="display-adverts text-left">
                    <?php for ($i = 0; $i < count($userAdverts); $i++) :;
                        $advert = $userAdverts[$i]; ?>
                        <div class="jumbotron jumbotron-fluid pb-4 my-5">
                            <div class="advert px-2 px-sm-5">
                                <div class="title pb-4">
                                    <a href="<?php echo $action ?>&advertOption=details&id=<?php echo $advert['id'] ?>">
                                        <p class="h2 text-dark"><?php echo $advert['title'] ?></p>
                                    </a>
                                </div>
                                <hr class="my-sm-4">
                                <div class="advert-data text-muted">
                                    <p><?php echo $advert['date'] ?> <i class="fas fa-calendar-alt"></i></p>
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                </section>

                <nav class="pagination pb-5">
                    <ul class="pagination w-100 d-flex justify-content-center">
                        <?php if ($pageNumber !== 1) : ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . 1 ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . $previous ?>">
                                    <i class="fas fa-angle-left"></i></a>
                                </a>
                            </li>
                        <?php endif ?>

                        <?php for ($i = 1; $i <= $countOfPages; $i++) : ?>
                            <li class="page-item <?php echo ($i === $pageNumber) ? 'active' : '' ?>">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . $i ?>">
                                    <span><?php echo $i ?></span>
                                </a>
                            </li>
                        <?php endfor ?>

                        <?php if ($pageNumber !== $countOfPages) : ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . $next ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $action . '&pageNumber=' . $countOfPages ?>">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        <?php endif ?>
                    </ul>
                </nav>

            <?php break;
                //Display one chosen advertisement with available options
            case ($userAdvert): ?>
                <section class="display-advert">
                    <div class="advert my-5 py-5">
                        <div class="row text-center text-md-left">
                            <div class="col-md-7 col-lg-8 mb-2 order-1 order-md-0">
                                <div class="border py-5 px-1 px-md-5 bg-light">
                                    <div class="title">
                                        <h2><?php echo $userAdvert['title'] ?></h2>
                                    </div>
                                    <hr class="mb-5">
                                    <div class="content">
                                        <p class="m-0"><?php echo $userAdvert['content'] ?></p>
                                    </div>
                                    <hr class="mt-5">
                                    <div class="date">
                                        <p class="m-0">
                                            <i class="fas fa-calendar-alt"></i> <?php echo $userAdvert['date'] ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 col-lg-4 mb-2">
                                <div class="border py-5 px-5 bg-light">
                                    <div class="options">
                                        <h2 class="text-center mb-5">Opcje</h2>
                                        <a class="btn btn-dark btn-block mb-3" href="<?php echo $action ?>&advertOption=edit&id=<?php echo $userAdvert['id'] ?>"><i class="fas fa-edit"></i> Edytuj</a>
                                        <a class="btn btn-dark btn-block mb-3" href="<?php echo $action ?>&advertOption=delete&id=<?php echo $userAdvert['id'] ?>"><i class="far fa-trash-alt"></i> Usuń</a>
                                        <a class="btn btn-dark btn-block" href="<?php echo $action ?>"><i class="fas fa-long-arrow-alt-left"></i> Powrót do listy</a>
                                    </div>
                                    <?php if ($delete === true) : ?>
                                        <div class="question mt-4 text-center">
                                            <p class="alert alert-danger">Czy na pewno chcesz usunąć to ogłoszenie?</p>
                                            <a class="btn btn-outline-dark" href="<?php echo $action ?>&advertOption=delete&id=<?php echo $userAdvert['id'] ?>&question=yes">Tak</a>
                                            <a class="btn btn-outline-dark" href="<?php echo $action ?>&advertOption=details&id=<?php echo $userAdvert['id'] ?>&question=no">Nie</a>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php break;

            default: ?>
                <a class="btn btn-dark mt-4" href="/?action=userPanel&subpage=addAdv">Dodaj swoje pierwsze ogłoszenie</a>
        <?php endswitch ?>
    </div>
</section>