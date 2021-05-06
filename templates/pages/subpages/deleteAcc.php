<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$confirm = $params['confirm'] ?? null;
?>
<section class="delete-acc">
    <div class="container text-center">
        <div class="user-title flex-column flex-md-row text-dark py-3 py-md-5">
            <i class="fas fa-trash-alt px-4"></i>
            <h2>Usuń konto</h2>
        </div>
        <div class="row text-center justify-content-center mb-5">
            <div class="col-sm-7 col-lg-5 border border-dark m-4 py-5">
                <p class="alert alert-danger">Uwaga!! Usuwając konto utracisz wszystkie dane bez możliwości ich przywrócenia.</p>
                <div class="destination py-5 px-2 px-sm-5">
                    <?php if ($confirm) : ?>
                        <p class="question">Czy na pewno chcesz usunąć konto?</p>
                        <form action="/?action=userPanel&subpage=deleteAcc" method="POST">
                            <input class="btn btn-dark my-4" type="submit" value="tak" name="save">
                            <input class="btn btn-dark" type="submit" value="nie" name="save">
                        </form>
                    <?php else : ?>
                        <form action="/?action=userPanel&subpage=deleteAcc" method="POST">
                            <div class="form-group mb-4">
                                <label for="password">Wpisz hasło:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <input class="btn btn-dark mt-4" type="submit" value="usuń" name="save">
                        </form>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</section>