<?php

declare(strict_types=1);

namespace Market\Model;

use Exception;
use Market\Exception\DatabaseException;
use Market\Exception\PageValidateException;
use Market\Exception\SubpageValidateException;
use Market\Exception\ValidateException;
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
            throw new ValidateException('Wprowadź wszystkie dane do formularza');
        }

        $login = $data['login'];
        $password = $data['password'];
        $passRepeat = $data['pass-repeat'];
        $email = $data['email'];

        //Username characters validation
        if (preg_match('/^[a-zA-Z0-9]+$/', $login) == 0) {
            throw new ValidateException('Niepoprawna nazwa użytkownika');
        }

        $passValid = $this->validatePasswords($password, $passRepeat);
        if ($passValid !== true) {
            throw new ValidateException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        //Check i email contains uppercase
        if (preg_match('/[A-Z]/', $email)) {
            throw new ValidateException('Niepoprawny adres email');
        }

        //Email validation
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

        //hash password
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

        try {
            //insert new account
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

    //Creating new advertisement
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

    //Insert user data
    public function addUserData(array $uData): bool
    {
        $idPlace = $uData['idPlace'];
        if ($idPlace === 0) {
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
}
