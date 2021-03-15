<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$confirm = $params['confirm'] ?? null;
?>


<?php if ($confirm) : ?>
    <p class="message">Czy na pewno chcesz usunąć konto?</p>
    <form action="/?action=userPanel&subpage=deleteAcc" method="POST">
        <input type="submit" value="tak" name="save">
        <input type="submit" value="nie" name="save">
    </form>
<?php else : ?>
    <form action="/?action=userPanel&subpage=deleteAcc" method="POST">
        <label for="password">Wpisz hasło:</label>
        <input type="password" name="password" id="password">
        <input type="submit" value="usuń" name="save">
    </form>
    <p class="message">Uwaga!! Klikając Usuń utracisz wszystkie dane bez możliwości ich przywrócenia.</p>
<?php endif ?>