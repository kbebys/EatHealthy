<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>
<section class="register">
    <div class="container-fluid">
        <div class="register-hero top-hero mb-5 mx-n4">
            <div class="hero-shadow"></div>
            <p class="text-light">Zarejestruj się</p>
        </div>
        <div class="row text-center justify-content-center mb-5">
            <div class="col-sm-7 col-lg-5 border border-dark m-4 py-5">
                <div class="destination py-5 px-2 px-sm-5">
                    <form action="/?action=register" method="post" autocomplete="off">
                        <div class="form-group mb-4">
                            <label for="username">Nazwa Użytkownika:</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                            <small class="form-text text-muted">Login może składać się wyłącznie z małych i dużych liter oraz liczb!!(Bez polskich znaków!!).</small>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password">Hasło:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <small class="form-text text-muted">Hasło musi mieć od 5 do 20 znaków, składać tylko z liter, cyfr i znaków specjalnych przy czym musi
                                zawierć przynajmniej
                                jedną
                                małą oraz dużą literę i jden znak specjalny!!</small>
                        </div>
                        <div class="form-group mb-4">
                            <label for="psw-repeat">Powtórz hasło:</label>
                            <input type="password" name="psw-repeat" id="psw-repeat" class="form-control" class="form-control" required>
                        </div>
                        <div class="form-group mb-5">
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="g-recaptcha d-flex justify-content-center" data-sitekey="6Ldn7rUaAAAAAErm09DnXWSAUA4iFSyTE2tJPjHY"></div>
                        <input class="btn btn-outline-dark mt-4" type="submit" name="save" value="Zarejestruj">
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>