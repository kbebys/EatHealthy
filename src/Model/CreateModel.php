<?php

declare(strict_types=1);

namespace Market\Model;

use Exception;
use Market\Exception\DatabaseException;
use Market\Exception\ErrorException;
use PDO;
use Throwable;

class CreateModel extends AbstractModel
{
    //Register new User
    public function register(array $data): bool
    {
        //trim() delete  whitespaces from beginning and end fo string
        $data = array_map('trim', $data);
        //check if given data are empty
        if ($this->validateEmpty($data)) {
            throw new ErrorException('Wprowadź wszystkie dane do formularza');
        }

        $login = $data['login'];
        $password = $data['password'];
        $passRepeat = $data['pass-repeat'];
        $email = $data['email'];

        //Username characters validation
        if (preg_match('/^[a-zA-Z0-9]+$/', $login) == 0) {
            throw new ErrorException('Niepoprawna nazwa użytkownika');
        }

        $passValid = $this->validatePassword($password, $passRepeat);
        if ($passValid !== true) {
            throw new ErrorException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        //Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ErrorException('Niepoprawny adres email');
        }

        $stmt = $this->checkUser('login', $login);
        if ($stmt != false) {
            throw new ErrorException('Użytkownik o tej nazwie istnieje');
        }

        $stmt = $this->checkUser('email', $email);
        if ($stmt != false) {
            throw new ErrorException('Konto z tym adresem email już istnieje');
        }

        //hash password
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

        try {
            //insert new account
            $query = "INSERT INTO user (login, password, email) VALUES (?, ?, ?)";
            $stmt = self::$conn->prepare($query);
            $stmt->bindParam(1, $login, PDO::PARAM_STR);
            $stmt->bindParam(2, $passwordHashed, PDO::PARAM_STR);
            $stmt->bindParam(3, $email, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    //Creating new advertisment
    public function addAdvertisment(array $advData): bool
    {
        $advData = array_map('trim', $advData);

        if ($this->validateEmpty($advData)) {
            throw new ErrorException('Uzupełnij wszytkie pola');
        }

        $title = $advData['title'];
        $kind = $advData['kind'];
        $content = $advData['content'];
        $place = $advData['place'];
        $id = (int) $_SESSION['id'];

        if (strlen($title) > 150) {
            throw new ErrorException('Wpisałeś za długi tytuł');
        }

        if ($kind !== 'sell' && $kind !== 'buy') {
            throw new ErrorException('Błąd wysyłania danych. Spróbuj jeszce raz');
        }

        if (strlen($place) > 150) {
            throw new ErrorException('Wpisałeś za długi tytuł');
        }

        try {
            $query = "INSERT INTO advertisment (id_user, title, content, kind_of_transaction, place) 
            VALUES (?, ?, ?, ?, ?)";
            $stmt = self::$conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $title, PDO::PARAM_STR);
            $stmt->bindParam(3, $content, PDO::PARAM_STR);
            $stmt->bindParam(4, $kind, PDO::PARAM_STR);
            $stmt->bindParam(5, $place, PDO::PARAM_STR);
            $stmt->execute();
        } catch (DatabaseException $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }

        return true;
    }

    //Insert user data
    public function sendUserData(array $uData): bool
    {
        $uName = $this->validateName($uData['uName']);
        if (!$uName) {
            throw new Exception('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $phone = $this->validatePhone($uData['phone']);
        if (!$phone) {
            throw new Exception('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $id = (int) $_SESSION['id'];

        try {
            $query = "INSERT INTO user_data (id_user, first_name, last_name, phone_number) VALUES (?, ?, 'x', ?)";
            $stmt = self::$conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $uName, PDO::PARAM_STR);
            $stmt->bindParam(3, $phone, PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }
}
