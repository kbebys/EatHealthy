<?php

declare(strict_types=1);

namespace Market\Model;

use Market\Exception\DatabaseException;
use Market\Exception\PageValidateException;
use Market\Exception\SubpageValidateException;
use Market\Exception\ValidateException;
use PDO;
use Throwable;

class CreateModel extends AbstractModel
{
    public function register(array $data, array $recaptchaSecret): bool
    {
        $data = array_map('trim', $data);

        if ($this->validateEmpty($data)) {
            throw new ValidateException('Wprowadź wszystkie dane do formularza');
        }

        $login = $data['login'];
        $password = $data['password'];
        $passRepeat = $data['pass-repeat'];
        $email = $data['email'];
        $recaptcha = ['userResponse' => $data['recaptcha'], 'secretKey' => $recaptchaSecret['secretKey']];

        //Login has to contain only lower and upper letters and numbers (without polish chars)
        if (preg_match('/^[a-zA-Z0-9]+$/', $login) == 0) {
            throw new ValidateException('Niepoprawna nazwa użytkownika');
        }

        $passValid = $this->validatePasswords($password, $passRepeat);
        if ($passValid !== true) {
            throw new ValidateException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        //Email cannot contain uppercase
        if (preg_match('/[A-Z]/', $email)) {
            throw new ValidateException('Niepoprawny adres email');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidateException('Niepoprawny adres email');
        }

        $stmt = $this->checkUserExist('login', $login);
        if ($stmt !== false) {
            throw new ValidateException('Użytkownik o tej nazwie istnieje');
        }

        $stmt = $this->checkUserExist('email', $email);
        if ($stmt !== false) {
            throw new ValidateException('Konto z tym adresem email już istnieje');
        }

        $this->recaptchaVerify($recaptcha);

        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

        try {
            $query = "INSERT INTO user (login, password, email) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $login, PDO::PARAM_STR);
            $stmt->bindParam(2, $passwordHashed, PDO::PARAM_STR);
            $stmt->bindParam(3, $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() !== 1) {
                throw new DatabaseException('Błąd dodawania użytkownika');
            }

            return true;
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function addAdvertisement(array $advData): bool
    {
        $advData = array_map('trim', $advData);

        if ($this->validateEmpty($advData)) {
            throw new PageValidateException('Uzupełnij wszytkie pola');
        }

        $title = $advData['title'];
        $kind = $advData['kind'];
        $content = $advData['content'];
        $id = (int) $_SESSION['id'];

        if (strlen($title) > 150) {
            throw new PageValidateException('Wpisałeś za długi tytuł');
        }

        if ($kind !== 'sell' && $kind !== 'buy') {
            throw new PageValidateException('Błąd wysyłania danych. Spróbuj jeszce raz');
        }

        try {
            $query = "INSERT INTO advertisements (id_user, title, content, kind_of_transaction) 
            VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $title, PDO::PARAM_STR);
            $stmt->bindParam(3, $content, PDO::PARAM_STR);
            $stmt->bindParam(4, $kind, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() !== 1) {
                throw new PageValidateException('Błąd dodawania danych');
            }

            return true;
        } catch (PageValidateException $e) {
            throw new PageValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function addUserData(array $uData): bool
    {
        $idPlace = $uData['idPlace'];

        if ($idPlace < 1) {
            throw new SubpageValidateException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $uName = $this->validateName($uData['uName']);
        if (!$uName) {
            throw new SubpageValidateException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $phone = $this->validatePhone($uData['phone']);
        if (!$phone) {
            throw new SubpageValidateException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $id = (int) $_SESSION['id'];

        try {
            $query = "INSERT INTO user_data (id_user, first_name, id_places, phone_number) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $uName, PDO::PARAM_STR);
            $stmt->bindParam(3, $idPlace, PDO::PARAM_INT);
            $stmt->bindParam(4, $phone, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() !== 1) {
                throw new SubpageValidateException('Błąd dodawania danych');
            }

            return true;
        } catch (SubpageValidateException $e) {
            throw new SubpageValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    private function recaptchaVerify(array $recaptcha): void
    {
        $secretKey = $recaptcha['secretKey'];
        $userResponse = $recaptcha['userResponse'];
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($userResponse);
        $responseData = file_get_contents($url);
        $responseData = json_decode($responseData, true);

        if (!$responseData['success']) {
            throw new ValidateException('Walidacja recaptcha nie powiodła się. Spróbuj ponownie!');
        }
    }
}
