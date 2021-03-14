<?php
if (!$loggedin) {
    header('Location: /?action=main');
    exit;
}

$errorWindow = $params['errorWindow'] ?? null;
$successWindow = $params['messageWindow'] ?? null;
?>
<div class="user-panel">
    <div class="user-menu">
        <div class="logo"><?php echo $_SESSION['name'] ?></div>
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