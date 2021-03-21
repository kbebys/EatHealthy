<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>

<p class="message">Dodaj ogłoszenie</p>
<form action="/?action=userPanel&subpage=addAdv" method="POST" autocomplete="off">
    <label for="title">Tytuł ogłoszenia:</label>
    <input type="text" id="title" name="title" maxlength="150" required>
    <label for="kind">Rodzaj:</label>
    <select name="kind" id="kind" required>
        <option disabled selected value> -- Wybierz rodzaj transakcji -- </option>
        <option value="sell">Sprzedam</option>
        <option value="buy">Kupię</option>
    </select>
    <label for="content">Treść ogłoszenia:</label>
    <textarea name="content" id="content" cols="100" rows="5" required></textarea>
    <label for="place">Miejscowość</label>
    <input type="text" name="place" id="place" maxlength="100">
    <input type="submit" name="save" value="Wystaw">
</form>