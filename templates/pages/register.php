<?php
if (empty($params['access'])) {
    header('Location: /?action=main');
    exit;
}
?>
<form action="/?action=register" method="post" autocomplete="off">
    <label for="username">Nazwa Użytkownika:*</label>
    <input type="text" name="username" id="username" required>
    <label for="password">Hasło:**</label>
    <input type="password" name="password" id="password" required>
    <label for="psw-repeat">Powtórz hasło:</label>
    <input type="password" name="psw-repeat" id="psw-repeat">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <div class="g-recaptcha" data-sitekey="6Ldn7rUaAAAAAErm09DnXWSAUA4iFSyTE2tJPjHY"></div>
    <!-- <input class="checkbox" type="checkbox"> -->
    <!-- <p class="checkbox">Akceptuję regulamin.</p> -->
    <input type="submit" name="save" value="Utwórz nowe konto">
</form>