<?php

declare(strict_types=1);

namespace Market\Model;

use Exception;
use Market\Exception\DatabaseException;
use Market\Exception\PageValidateException;
use Market\Exception\SubpageValidateException;
use PDO;
use PDOException;
use Throwable;

abstract class AbstractModel
{
    protected PDO $conn;

    public function __construct(array $config)
    {
        try {
            $this->validateConfig($config);
            $this->createConneciton($config);
        } catch (PDOException $e) {
            throw new Exception('Błąd połączenia', 400, $e);
        }
    }

    private function createConneciton(array $config): void
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']}";

        $this->conn = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                //Turn off emulated prepare statements and use real
                PDO::ATTR_EMULATE_PREPARES => false,
                //Throw PDOException by default]
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    private function validateConfig(array $config): void
    {
        if (
            empty($config['host'])
            || empty($config['database'])
            || empty($config['user'])
            || empty($config['password'])
        ) {
            throw new Exception('Błąd konfiguracji bazy danych');
        }
    }

    protected function checkUserExist(string $param, $user)
    {
        try {
            $query = "SELECT id, password FROM user WHERE BINARY $param = ?";
            $stmt = $this->conn->prepare($query);
            if (is_int($user)) {
                $stmt->bindParam(1, $user, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $user, PDO::PARAM_STR);
            }
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return false;
            } else {
                return $stmt;
            }
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }


    protected function validateEmpty(array $data): bool
    {
        foreach ($data as $value) {
            if (empty($value)) {
                return true;
            }
        }
        return false;
    }

    protected function validateName(string $uName)
    {
        $uName = trim($uName);

        if (empty($uName)) {
            throw new SubpageValidateException('Wprowadź dane do formularza');
        }

        if (preg_match('/^[a-zA-ZżźćńółęąśŻŹĆĄŚĘŁÓŃ]+$/', $uName) == 0) {
            throw new SubpageValidateException('Niedozwolone znaki w imieniu');
        }

        //string to lowercase
        $uName = strtolower($uName);
        //first letter of string to uppercase
        $uName = ucfirst($uName);


        return $uName;
    }

    protected function validatePhone(string $phone)
    {
        $phone = trim($phone);

        $phone = str_replace(['-', ' '], '', $phone);

        if (empty($phone)) {
            throw new SubpageValidateException('Wprowadź dane do formularza');
        }

        if (preg_match('/^[0-9]{9}$/', $phone) == 0) {
            throw new SubpageValidateException('Nieprawidłowy numer telefonu');
        }

        return (int) $phone;
    }

    protected function validatePasswords(string $pass, string $passR): bool
    {
        //Password validation(5 - 20 characters, minimum of 1 uppercase char, minimum of 1 lowercase char, minimum 1 digit )
        $passValidPattern1 = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{5,20}$/';
        //Passwor only can contain this char
        $passValidPattern2 = '/^[a-zA-Z0-9!@#$%^&*-]+$/';
        if (preg_match($passValidPattern1, $pass) == 0) {
            throw new PageValidateException('Podane hasło nie spełnia wymogów!!');
        } elseif (preg_match($passValidPattern2, $pass) == 0) {
            throw new PageValidateException('Podane hasło zawiera niedozwolone znaki!!');
        } //checking if both passwords are the same
        elseif ($pass !== $passR) {
            throw new PageValidateException('podane hasła nie są takie same!!');
        } else {
            return true;
        }
    }

    protected function checkAdvertisementExist(int $idUser, int $idAdv): bool
    {
        try {
            $query = "SELECT count(*) AS count FROM advertisements WHERE id = ? AND id_user = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $idAdv, PDO::PARAM_INT);
            $stmt->bindParam(2, $idUser, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] === 1) {
                return true;
            } else {
                return false;
            }
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }
}
