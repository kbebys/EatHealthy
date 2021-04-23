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
    <title>Ryneczek</title>
    <link rel="stylesheet" href="public/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <div class="wrapper">
        <header>
            <div class="logo"><a href="/">Ryneczek</a></div>
            <div class="menu">
                <?php if ($loggedin) : ?>
                    <div style="display: none;"><a href="/?action=login">Logowanie</a></div>
                    <div><a href="/?action=logout">Wyloguj</a></div>
                    <div><a href="/?action=userPanel"><?php echo $_SESSION['name']; ?></a></div>
                <?php else : ?>
                    <div><a href="/?action=login">Logowanie</a></div>
                    <div><a href="/?action=register">Rejestracja</a></div>
                <?php endif ?>
            </div>
        </header>
        <main>
            <div class="content">
                <?php if ($success) : ?>
                    <p class="message success"><?php echo $success; ?></p>
                <?php endif ?>
                <?php if ($error) : ?>
                    <p class="message error"><?php echo $error; ?></p>
                <?php endif ?>
                <?php require_once("templates/pages/$page.php") ?>
                <?php if ($page === 'register') : ?>
                    <p class="info">*Login może składać się wyłącznie z małych i dużych liter oraz liczb!!(Bez polskich znaków!!)</p>
                    <p class="info">**Hasło musi mieć od 5 do 20 znaków, składać tylko z liter, cyfr i znaków specjalnych przy czym musi
                        zawierć przynajmniej
                        jedną
                        małą oraz dużą litere i jden znak specjalny!!</p>
                <?php endif ?>
            </div>
        </main>
        <footer>
            <p>Ryneczek Bolka &copy; wszelkie prawa zastrzeżone</p>
        </footer>
    </div>
</body>

</html>