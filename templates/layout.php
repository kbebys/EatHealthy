<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$loggedin = $_SESSION['loggedin'] ?? null;
$success = $params['message'] ?? null;
$error = $params['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EatHealthy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" integrity="sha384-SZXxX4whJ79/gErwcOYf+zWLeJdY/qpuqC4cAa9rOGUstPomtqpuNWT9wdPEn2fk" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark b-red py-4 sticky-top">
        <div class="container d-flex flex-column flex-lg-row">

            <?php if ($loggedin) : ?>
                <div style="display: none;"><a href="/?action=login">Logowanie</a></div>
                <div><a href="/?action=logout">Wyloguj</a></div>
                <div><a href="/?action=userPanel"><?php echo $_SESSION['name']; ?></a></div>

            <?php else : ?>
                <a class="navbar-brand" href="/?action=main">
                    <span class="c-d-red">Eat</span><i class="fas fa-carrot c-br-orange"></i>Healthy
                </a>
                <div class="navbar-nav ml-lg-auto">
                    <hr class="text-light d-lg-none">
                    <a class="nav-link active" href="/?action=login"><i class="fas fa-store"></i> Moje stanowisko</a>
                </div>
            <?php endif ?>
        </div>
    </nav>


    <main>
        <?php if ($success) : ?>
            <div class="alert alert-success message m-2" role="alert">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif ?>
        <?php if ($error) : ?>
            <div class="alert alert-danger message m-2" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif ?>

        <?php require_once("templates/pages/$page.php") ?>

        <?php if ($page === 'register') : ?>
            <p class="info">*Login może składać się wyłącznie z małych i dużych liter oraz liczb!!(Bez polskich znaków!!)</p>
            <p class="info">**Hasło musi mieć od 5 do 20 znaków, składać tylko z liter, cyfr i znaków specjalnych przy czym musi
                zawierć przynajmniej
                jedną
                małą oraz dużą litere i jden znak specjalny!!</p>
        <?php endif ?>
    </main>
    <footer class="py-4 b-red text-white text-center">
        <p class="m-0">EatHealthy &copy; 2021. Wszelkie prawa zastrzeżone</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    <script>
        $(init);

        function init() {
            $('.destination').prepend($('.message'));
        }
    </script>
</body>

</html>