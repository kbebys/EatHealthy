<?php
if (!$loggedin) {
    header('Location: /?action=main');
    exit;
}
?>

<header class="user-panel-header">
    <div class="collapse bg-light text-center pt-5" id="navbarUserPanel">
        <div class="container">
            <div class="options row border-bottom border-dark pb-4">
                <div class="col-md-6">
                    <div class="adverts-options d-flex flex-column">
                        <a href="/?action=userPanel&subpage=addAdv"><i class="fas fa-calendar-plus"></i> Dodaj ogłoszenie</a>
                        <a href="/?action=userPanel&subpage=myAdv"><i class="fas fa-table"></i> Moje ogłoszenia</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="user-options d-flex flex-column">
                        <a href=" /?action=userPanel&subpage=myData"><i class="fas fa-address-card"></i> Moje dane</a>
                        <a href="/?action=userPanel&subpage=changePass"><i class="fas fa-lock"></i> Zmień Hasło</a>
                        <a href="/?action=userPanel&subpage=deleteAcc"><i class="fas fa-trash-alt"></i> Usuń konto</a>
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
                    <p class="m-0 c-red"><?php echo $_SESSION['login'] ?></p>
                </div>
            </div>
        </div>
    </nav>
</header>
<?php require_once ("templates/pages/subpages/" . $subpage) . ".php" ?>