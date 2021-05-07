<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>

<section class="error py-5">
    <div class="container text-center py-5">
        <p class="display-4">Wystąpił błąd!!</p>
        <div class="destination"></div>
        <a href="/?action=main" class="btn btn-dark"><i class="fas fa-arrow-left"></i> Powrót do strony głownej</a>
    </div>
</section>