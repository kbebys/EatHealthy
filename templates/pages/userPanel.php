<?php
if (!$loggedin) {
    header('Location: /?action=main');
    exit;
}

$errorWindow = $params['errorWindow'] ?? null;
$successWindow = $params['messageWindow'] ?? null;
?>

<header class="user-panel-header">
    <div class="collapse bg-light text-center pt-5" id="navbarUserPanel">
        <div class="container">
            <div class="options row border-bottom border-dark pb-4">
                <div class="col-md-6">
                    <div class="adverts-options d-flex flex-column">
                        <a href="/?action=userPanel&subpage=addAdv">Dodaj ogłoszenie</a>
                        <a href="/?action=userPanel&subpage=myAdv">Moje ogłoszenia</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="user-options d-flex flex-column">
                        <a href=" /?action=userPanel&subpage=myData">Moje dane</a>
                        <a href="/?action=userPanel&subpage=changePass">Zmień Hasło</a>
                        <a href="/?action=userPanel&subpage=deleteAcc">Usuń konto</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-light bg-light border-bottom">
        <div class="container">
            <div class="navbar-toggler w-100" type="button" data-toggle="collapse" data-target="#navbarUserPanel" aria-controls="navbarUserPanel" aria-expanded="false" aria-label="Toggle navigation">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-md-between">
                    <i class="fas fa-user-circle order-md-1 pb-2 pb-md-0"></i>
                    <p class="m-0"><?php echo $_SESSION['login'] ?></p>
                </div>
            </div>
        </div>
    </nav>
</header>
<div class="user-panel">
    <div class="user-menu">
        <div class="logo"><?php echo $_SESSION['login'] ?></div>
        <ul>
            <li><a href="/?action=userPanel&subpage=addAdv">Dodaj ogłoszenie</a></li>
            <li><a href="/?action=userPanel&subpage=myAdv">Moje ogłoszenia</a></li>
            <li><a href="/?action=userPanel&subpage=myData">Moje dane</a></li>
            <li><a href="/?action=userPanel&subpage=changePass">Zmień Hasło</a></li>
            <li><a href="/?action=userPanel&subpage=deleteAcc">Usuń konto</a></li>
            </li>
        </ul>
    </div>
    <div class="user-window">
        <?php if ($errorWindow) : ?>
            <p class="message error"><?php echo $errorWindow  ?></p>
        <?php endif ?>
        <?php if ($successWindow) : ?>
            <p class="message success"><?php echo $successWindow ?></p>
        <?php endif ?>
        <?php require_once ("templates/pages/subpages/" . $subpage) . ".php" ?>
    </div>
</div>