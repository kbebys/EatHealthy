<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$loggedin = $_SESSION['loggedin'] ?? null;
$success = $params['success'] ?? null;
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
            <a class="navbar-brand" href="/?action=main">
                <span class="text-dark">Eat</span><i class="fas fa-carrot c-br-orange"></i>Healthy
            </a>
            <?php if ($loggedin) : ?>
                <div class="navbar-nav ml-lg-auto">
                    <hr class="text-light d-lg-none">
                    <a class="nav-link text-light mr-lg-4" href="/?action=userPanel"><i class="fas fa-store text-dark"></i> Moje stanowisko</a>
                    <a class="nav-link text-light" href="/?action=logout"><i class="fas fa-sign-out-alt text-dark"></i> Wyloguj</a>
                </div>
            <?php else : ?>
                <div class="navbar-nav ml-lg-auto">
                    <hr class="text-light d-lg-none">
                    <a class="nav-link text-light" href="/?action=login"><i class="fas fa-store text-dark"></i> Moje stanowisko</a>
                </div>
            <?php endif ?>
        </div>
    </nav>


    <main>
        <?php if ($success) : ?>
            <div class="alert alert-success message m-4" role="alert">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif ?>
        <?php if ($error) : ?>
            <div class="alert alert-danger message m-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif ?>

        <?php require_once("templates/pages/$page.php") ?>
    </main>
    <footer class="py-4 b-red text-white text-center">
        <p class="m-0">EatHealthy &copy; 2021. Wszelkie prawa zastrzeżone</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    <script>
        $(init);
        var arrow = $('i#arrow');

        function init() {
            $('.destination').prepend($('.message'));
        }

        $('.user-panel').click(function(event) {
            var open = $('.collapse').hasClass("show");
            console.log(open);
            if (!open) {
                arrow.removeClass('fas fa-chevron-down').addClass('fas fa-chevron-up');
            } else {
                arrow.removeClass('fas fa-chevron-up').addClass('fas fa-chevron-down');
            }
        })

        $(document).click(function(event) {
            var clickover = $(event.target);
            var _opened = $(".collapse").hasClass("show");
            if (_opened === true && !clickover.hasClass("navbar-toggler")) {
                $(".navbar-toggler").click();
                arrow.removeClass('fas fa-chevron-up').addClass('fas fa-chevron-down');
            }
        })
    </script>
</body>

</html>