<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>
<h1>Dodaj ogłoszenie</h1>