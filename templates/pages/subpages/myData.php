<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$listOfPlaces = $params['listOfPlaces'] ?? [];

$uData = $params['uData'] ?? [];
$name = $uData['name'] ?? null;
$phone = $uData['phone'] ?? null;
$email = $uData['email'] ?? null;
$place = $params['uData']['place'] ?? null;

$changeData = $params['change'] ?? null;

$subpage = "/?action=userPanel&subpage=myData";
?>

<section class="user-data">
    <div class="container text-center">
        <div class="user-title flex-column flex-md-row text-dark py-3 py-md-5">
            <i class="fas fa-address-card px-4"></i>
            <h2>Moje dane</h2>
        </div>
        <!-- Display form with content which user wants to change -->
        <?php if ($changeData) : ?>
            <div class="change-data border border-dark my-5 py-5 px-3 px-md-5">
                <form action="<?php echo $subpage ?>&change=<?php echo $changeData ?>" method="POST">
                    <?php switch ($changeData):
                        case 'name': ?>
                            <div class="form-group mb-4">
                                <label for="name">Imię: </label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        <?php break;
                        case 'phone': ?>
                            <label for="phone">Numer telefonu: </label>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <p class="input-group-text">+48</p>
                                </div>
                                <input type="tel" name="phone" id="phone" class="form-control" required>
                            </div>
                        <?php break;
                        case 'place': ?>
                            <div class="form-group mb-4">
                                <select name="place" id="place" class="form-control" required>
                                    <option disabled selected value> -- Wybierz swoją miejscowość -- </option>
                                    <?php
                                    foreach ($params['listOfPlaces'] as $key) {
                                        echo '<option value="' . $key['id'] . '">' . $key['place'] . ' (gmina: ' . $key['community'] . ')</option>';
                                    } ?>
                                </select>
                            </div>
                    <?php break;
                    endswitch ?>
                    <input class="btn btn-outline-dark my-3" type="submit" name="save" value="Zmień">
                </form>
                <a class="btn c-d-red" href="<?php echo $subpage ?>">
                    <i class="fas fa-arrow-up"></i> schowaj <i class="fas fa-arrow-up"></i>
                </a>
            </div>
        <?php endif ?>
        <?php if ($uData) : ?>
            <!-- Table with user data -->
            <div class="destination"></div>
            <div class="show-data mt-5 d-flex flex-column flex-md-row">
                <table class="table mx-md-2">
                    <thead class=" b-d-red text-light">
                        <tr>
                            <th scope="col">Imię</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $name ?></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-dark" href="<?php echo $subpage ?>&change=name">Zmień</a></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table mx-md-2">
                    <thead class=" b-d-red text-light">
                        <tr>
                            <th scope="col">Numer Telefonu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>+48 <?php echo $phone ?></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-dark" href="<?php echo $subpage ?>&change=phone">Zmień</a></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table mx-md-2">
                    <thead class=" b-d-red text-light">
                        <tr>
                            <th scope="col">Miejscowość</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $place ?></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-dark" href="<?php echo $subpage ?>&change=place">Zmień</a></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table mx-md-2">
                    <thead class=" b-d-red text-light">
                        <tr>
                            <th scope="col">Adres email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-muted"><?php echo $email ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <!-- When user did not add its data -->
            <div class="add-data row text-center justify-content-center mb-5">
                <div class="col-sm-7 col-lg-5 border border-dark m-4 py-5">
                    <p class="alert alert-info">Uzupełnij swoje dane aby móc w pełni korzystać z serwisu!!</p>
                    <div class="destination py-5 px-2 px-sm-5">
                        <form action="<?php echo $subpage ?>" method="POST">
                            <div class="form-group mb-4">
                                <label for="first-name">Imie: </label>
                                <input type="text" name="first-name" id="first-name" class="form-control" required>
                            </div>
                            <label for="phone-number">Numer telefonu:</label>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <p class="input-group-text">+48</p>
                                </div>
                                <input type="tel" name="phone-number" id="phone-number" class="form-control" required>
                            </div>
                            <div class="form-group mb-4 mt-5">
                                <select name="place" id="place" class="form-control" required>
                                    <option disabled selected value> -- Wybierz swoją miejscowość -- </option>
                                    <?php
                                    foreach ($listOfPlaces as $key) {
                                        echo '<option value="' . $key['id'] . '">' . $key['place'] . ' (gmina: ' . $key['community'] . ')</option>';
                                    } ?>
                                </select>
                            </div>
                            <input class="btn btn-dark mt-4" type="submit" name="save" value="Dodaj">
                        </form>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</section>