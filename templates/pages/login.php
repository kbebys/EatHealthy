<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>

<section id="login" class="container-fluid px-0 py-5 b-br-orange">
    <div class="login-box bg-light rounded-bottom">
        <div class="login-header top-hero">
            <div class="hero-shadow"></div>
            <p class="text-light">Zaloguj się</p>
        </div>
        <div class="destination p-5 mt-4">
            <form action="/?action=login" method="post">
                <div class="form-group">
                    <label for="username">Nazwa Użytkownika:</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Hasło:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <input class="btn btn-outline-dark btn-block my-5" type="submit" name="save" value="Zaloguj">
            </form>
        </div>
    </div>
</section>