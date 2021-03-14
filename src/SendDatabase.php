<?php

declare(strict_types=1);

namespace Market;

use Exception;
use Market\Exception\ErrorException;
use PDO;
use Throwable;


class SendDatabase extends AbstractDatabase
{

    //Validate login system
    public function login(array $data): bool
    {
        //trim() delete  whitespaces from beginning and end fo string
        $data = array_map('trim', $data);
        //check if given data are empty
        if ($this->validateEmpty($data)) {
            throw new ErrorException('Wprowadź dane logowania');
        }

        $login = $data['login'];
        $password = $data['password'];

        $stmt = $this->checkUser('login', $login);
        if ($stmt == false) {
            throw new ErrorException('Niepoprawna nazwa użytkownika');
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //veryfing passwords, both given from user and hashed from db
        if (!password_verify($password, $result['password'])) {
            throw new ErrorException('Niepoprawna nazwa użytkownika lub hasło');
        }

        //Creating session to know the user is logged in
        session_regenerate_id();

        $_SESSION['loggedin'] = true;
        $_SESSION['name'] = $login;
        $_SESSION['id'] = $result['id'];

        return true;
    }

    //Validate register system
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
        if ($passValid !== 'true') {
            throw new ErrorException($passValid);
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
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $login, PDO::PARAM_STR);
            $stmt->bindParam(2, $passwordHashed, PDO::PARAM_STR);
            $stmt->bindParam(3, $email, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (Throwable $e) {
            throw new Exception('Problem z połączeniem z bazą danych ' . $e->getMessage());
        }
    }

    //validate and insert new user data
    public function sendUserData(array $uData): string
    {
        $uName = $this->validateName($uData['uName']);
        if (is_string($uName)) {
            return $uName;
        }
        $uName = $uName[0];

        $phone = $this->validatePhone($uData['phone']);
        if (is_string($phone)) {
            return $phone;
        }

        $id = (int) $_SESSION['id'];

        try {
            $query = "INSERT INTO user_data (id_user, first_name, last_name, phone_number) VALUES (?, ?, 'x', ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $uName, PDO::PARAM_STR);
            $stmt->bindParam(3, $phone, PDO::PARAM_INT);
            $stmt->execute();

            return 'added';
        } catch (Throwable $e) {
            throw new Exception('Problem z połączeniem z bazą danych ' . $e->getMessage());
        }
    }

    //function to changing user first name
    public function changeName(string $uName): string
    {
        $uName = $this->validateName($uName);

        if (is_string($uName)) {
            return $uName;
        }

        $uName = $uName[0];


        $id = (int) $_SESSION['id'];


        try {
            $query = "UPDATE user_data SET first_name = ? WHERE id_user = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $uName, PDO::PARAM_STR);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            return "changed";
        } catch (Exception $e) {
            throw new Exception('Problem z połączeniem z bazą danych ' . $e->getMessage());
        }
    }

    public function changePhone(string $phone): string
    {
        $phone = $this->validatePhone($phone);

        if (is_string($phone)) {
            return $phone;
        }

        $id = (int) $_SESSION['id'];

        try {
            $query = "UPDATE user_data SET phone_number = ? WHERE id_user = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $phone, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            return "changed";
        } catch (Throwable $e) {
            throw new Exception('Problem z połączeniem z bazą danych ' . $e->getMessage());
        }
    }

    //Validate and insert new password
    public function changePassword(array $data): string
    {
        $data = array_map('trim', $data);

        if ($this->validateEmpty($data)) {
            return  'Wprowadź dane';
        }

        $old = $data['old'];
        $new = $data['new'];
        $newRepeat = $data['newRepeat'];
        $id = (int) $_SESSION['id'];

        if ($old === $new) {
            return 'Nowe hasło musi być inne od aktualnego';
        }

        try {
            $stmt = $this->checkUser('id', $id);

            if (!$stmt) {
                throw new Exception("Błąd");
            }

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!password_verify($old, $result['password'])) {
                return  'Niepoprawne aktualne hasło';
            }

            $passValid = $this->validatePassword($new, $newRepeat);
            if ($passValid !== 'true') {
                return $passValid;
            }

            $passwordHashed = password_hash($new, PASSWORD_DEFAULT);

            $query = "UPDATE user SET password = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $passwordHashed, PDO::PARAM_STR);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            return 'changed';
        } catch (Exception $e) {
            throw new Exception('Problem z połączeniem z bazą danych ' . $e->getMessage());
        }
    }

    public function deleteAcc(): void
    {
        try {
            $id = (int) $_SESSION['id'];

            $query = "DELETE FROM user_data WHERE id_user = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();


            $query = "DELETE FROM user WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            throw new Exception('Problem z połączeniem z bazą danych ' . $e->getMessage());
        }
    }

    private function validateEmpty(array $data): bool
    {
        foreach ($data as $value) {
            if (empty($value)) {
                return true;
            }
        }
        return false;
    }

    private function validatePassword(string $pass, string $passR): string
    {
        //Password validation(5 - 20 characters, minimum of 1 uppercase char, minimum of 1 lowercase char, minimum 1 digit )
        $passValidPattern1 = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{5,20}$/';
        //Passwor only can contain this char
        $passValidPattern2 = '/^[a-zA-Z0-9!@#$%^&*-]+$/';
        if (preg_match($passValidPattern1, $pass) == 0) {
            return 'Podane hasło nie spełnia wymogów!!';
        } elseif (preg_match($passValidPattern2, $pass) == 0) {
            return 'Podane hasło zawiera niedozwolone znaki!!';
        } //checking if both passwords are the same
        elseif ($pass !== $passR) {
            return 'podane hasła nie są takie same!!';
        } else {
            return 'true';
        }
    }

    private function validatePhone(string $phone)
    {
        $phone = trim($phone);

        $phone = str_replace(['-', ' '], '', $phone);

        if (empty($phone)) {
            return 'Wprowadź dane do formularza';
        }

        if (preg_match('/^[0-9]{9}$/', $phone) == 0) {
            return 'Nieprawidłowy numer telefonu';
        }

        return (int) $phone;
    }

    private function validateName(string $uName)
    {
        $uName = trim($uName);

        if (empty($uName)) {
            return 'Wprowadź dane do formularza';
        }

        if (preg_match('/^[a-zA-ZżźćńółęąśŻŹĆĄŚĘŁÓŃ]+$/', $uName) == 0) {
            return 'Niedozwolone znaki w imieniu';
        }

        //string to lowercase
        $uName = strtolower($uName);
        //first letter of string to uppercase
        $uName = ucfirst($uName);


        return $uName = [$uName];
    }
}
