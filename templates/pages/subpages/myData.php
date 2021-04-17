<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$ListOfPlaces = $params['listOfPlaces'] ?? [];

$uData = $params['uData'] ?? [];
$name = $uData['name'] ?? null;
$phone = $uData['phone'] ?? null;
$email = $uData['email'] ?? null;
$place = $params['uData']['place'] ?? null;

$changeData = $params['change'] ?? null;

$subpage = "/?action=userPanel&subpage=myData";
?>
<?php if ($uData) : ?>
    <table>
        <tr>
            <th>Imie</th>
            <th>Numer Telefonu</th>
            <th>Miejscowość</th>
            <th>Adres email</th>
        </tr>
        <tr>
            <td><?php echo $name ?></td>
            <td><?php echo $phone ?></td>
            <td><?php echo $place ?></td>
            <td><?php echo $email ?></td>
        </tr>
        <tr>
            <td><a href="<?php echo $subpage ?>&change=name">Zmień</a></td>
            <td><a href="<?php echo $subpage ?>&change=phone">Zmień</a></td>
            <td><a href="<?php echo $subpage ?>&change=place">Zmień</a></td>
        </tr>
    </table>

<?php else : ?>
    <!-- When user did not add its data -->
    <form action="<?php echo $subpage ?>" method="POST">
        <label for="first-name">Imie: </label>
        <input type="text" name="first-name" id="first-name" required>
        <label for="phone-number">Numer telefonu: </label>
        <p>+48 <input type="tel" name="phone-number" id="phone-number" value="" required></p>
        <select name="place" id="place" required>
            <option disabled selected value> -- Wybierz swoją miejscowość -- </option>
            <?php
            foreach ($params['listOfPlaces'] as $key) {
                echo '<option value="' . $key['id'] . '">' . $key['place'] . ' (gmina: ' . $key['community'] . ')</option>';
            } ?>
        </select>
        <input type="submit" name="save" value="Dodaj">
    </form>
    <p class="message">Uzupełnij swoje dane aby móc w pełni korzystać z serwisu!!</p>
<?php endif ?>

<!-- Display form with content which user wants to change -->
<?php if ($changeData) : ?>
    <form action="<?php echo $subpage ?>&change=<?php echo $changeData ?>" method="POST">
        <?php switch ($changeData):
            case 'name': ?>

                <label for="name">Imie: </label>
                <input type="text" name="name" id="name" required>

            <?php break;
            case 'phone': ?>

                <label for="phone">Numer telefonu: </label>
                <p>+48 <input type="tel" name="phone" id="phone" value="" required></p>

            <?php break;
            case 'place': ?>

                <select name="place" id="place" required>
                    <option disabled selected value> -- Wybierz swoją miejscowość -- </option>
                    <?php
                    foreach ($params['listOfPlaces'] as $key) {
                        echo '<option value="' . $key['id'] . '">' . $key['place'] . ' (gmina: ' . $key['community'] . ')</option>';
                    } ?>
                </select>

        <?php break;
        endswitch ?>

        <input type="submit" name="save" value="Zmień">
    </form>
    <a href="<?php echo $subpage ?>">zwiń</a>
<?php endif ?>