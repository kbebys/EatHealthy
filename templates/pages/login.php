<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>
<form action="/?action=login" method="post">
    <label for="username">Nazwa Użytkownika:</label>
    <input type="text" name="username" id="username" required>
    <label for="password">Hasło:</label>
    <input type="password" name="password" id="password" required>
    <input type="submit" name="save" value="Zaloguj">
</form>