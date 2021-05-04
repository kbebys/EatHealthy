<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>

<section class="add-advert">
    <div class="container text-center">
        <div class="user-title flex-column flex-md-row text-dark py-3 py-md-5">
            <i class="fas fa-calendar-plus px-4"></i>
            <h2>Dodaj ogłoszenie</h2>
        </div>
        <div class="row text-center justify-content-center mb-5">
            <div class="col-md-10 border border-dark m-4 py-5">
                <div class="destination py-5 px-2 px-sm-5">
                    <form action="/?action=userPanel&subpage=addAdv" method="POST" autocomplete="off">
                        <div class="form-group mb-4">
                            <label for="title">Tytuł ogłoszenia:</label>
                            <input type="text" id="title" class="form-control" name="title" maxlength="150" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="kind">Rodzaj:</label>
                            <select name="kind" id="kind" class="form-control" required>
                                <option disabled selected value> -- Wybierz rodzaj transakcji -- </option>
                                <option value="sell">Sprzedam</option>
                                <option value="buy">Kupię</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="content">Treść ogłoszenia:</label>
                            <textarea name="content" id="content" class="form-control" rows="8" required></textarea>
                        </div>
                        <input class="btn btn-dark mt-4" type="submit" name="save" value="Wystaw">
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>