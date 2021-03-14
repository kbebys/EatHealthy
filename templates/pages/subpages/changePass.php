<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>

<form action="/?action=userPanel&subpage=changePass" method="POST">
    <label for="password">Aktualne hasło:</label>
    <input type="password" name="password" id="password" required>
    <label for="new-password">Nowe hasło:</label>
    <input type="password" name="new-password" id="new-password" required>
    <label for="new-repeat-password">Powtórz nowe hasło:</label>
    <input type="password" name="new-repeat-password" id="new-repeat-password" required>
    <input type="submit" value="Zmień" name="save">
</form>