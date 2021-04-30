<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}

$advert = $params['advert'] ?? [];
$advert['first_name'] = ucfirst($advert['first_name']);
?>

<section class="detail-advert">
    <div class="container">
        <div class="advert my-5 py-5">
            <div class="row text-center text-md-left">
                <div class="col-md-7 col-lg-8 mb-2">
                    <div class="border py-5 px-1 px-md-5 bg-light">
                        <div class="title">
                            <h2><?php echo $advert['title'] ?></h2>
                        </div>
                        <hr class="mb-5">
                        <div class="content">
                            <p><?php echo $advert['content'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-lg-4 mb-2">
                    <div class="border py-5 px-1 px-md-5 bg-light">
                        <div class="advert-data">
                            <h2 class="text-center mb-4">Dane kontaktowe</h2>
                            <h3><i class="fas fa-calendar-alt"></i> Data wystawienia:</h3>
                            <p class="mb-4"><?php echo $advert['date'] ?></p>
                            <h3><i class="fas fa-smile-beam"></i> Imię:</h3>
                            <p class="mb-4"><?php echo $advert['first_name'] ?></p>
                            <h3><i class="fas fa-phone"></i> Numer telefonu:</h3>
                            <p class="mb-4"><?php echo $advert['phone_number'] ?></p>
                            <h3><i class="fas fa-map-marker-alt"></i> Lokalizacja:</h3>
                            <p class="mb-4"><?php echo $advert['place'] ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a class="btn btn-outline-dark btn-block" href="javascript:history.go(-1)"><i class="fas fa-long-arrow-alt-left"></i> Powrót</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>