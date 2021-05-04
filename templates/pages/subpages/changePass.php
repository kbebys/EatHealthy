<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>

<section class="change-pass">
    <div class="container text-center">
        <div class="user-title flex-column flex-md-row text-dark py-3 py-md-5">
            <i class="fas fa-lock px-4"></i>
            <h2>Zmień hasło</h2>
        </div>
        <div class="row text-center justify-content-center mb-5">
            <div class="col-sm-7 col-lg-5 border border-dark m-4 py-5">
                <div class="destination py-5 px-2 px-sm-5">
                    <form action="/?action=userPanel&subpage=changePass" method="POST">
                        <div class="form-group mb-4">
                            <label for="password">Aktualne hasło:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="new-password">Nowe hasło:</label>
                            <input type="password" name="new-password" id="new-password" class="form-control" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="new-repeat-password">Powtórz nowe hasło:</label>
                            <input type="password" name="new-repeat-password" id="new-repeat-password" class="form-control" required>
                        </div>
                        <input class="btn btn-dark mt-4" type="submit" value="Zmień" name="save">
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>