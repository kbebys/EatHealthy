<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>

<section class="login">
    <div class="container-fluid">
        <div class="login-hero top-hero mb-5 mx-n4">
            <div class="hero-shadow"></div>
            <p class="text-light">Zaloguj się</p>
        </div>
        <div class="row text-center justify-content-center mb-5">
            <div class="col-md-7 col-lg-5 border border-dark m-4 py-5">
                <div class="destination p-5">
                    <form action="/?action=login" method="post">
                        <div class="form-group mb-5">
                            <label for="username">Nazwa Użytkownika:</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Hasło:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <input class="btn btn-outline-dark mt-4" type="submit" name="save" value="Zaloguj">
                    </form>
                </div>
                <hr>
                <p>Jeżeli jeszcze nie mazsz konta:</p>
                <a href="/?action=register" class="link">Zarejestruj się</a>
            </div>
        </div>
    </div>
</section>