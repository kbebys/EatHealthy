<?php

declare(strict_types=1);

namespace Market\Model;

use Exception;
use Market\Exception\DatabaseException;
use Market\Exception\ErrorException;
use PDO;
use PDOException;
use Throwable;

abstract class AbstractModel
{
    protected static PDO $conn;

    public static function initConnection(array $config): void
    {
        try {
            self::validateConfig($config);
            self::createConneciton($config);
        } catch (PDOException $e) {
            throw new Exception('Błąd połączenia');
        }
    }

    private static function createConneciton(array $config): void
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']}";

        self::$conn = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [ //Throw PDOException by default]
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    private static function validateConfig(array $config): void
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

    protected function checkUser(string $param, $user)
    {
        try {
            $query = "SELECT id, password FROM user WHERE $param = ?";
            $stmt = self::$conn->prepare($query);
            if (is_int($user)) {
                $stmt->bindParam(1, $user, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $user, PDO::PARAM_STR);
            }
            $stmt->execute();
            //checking if this row exist
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
            throw new ErrorException('Wprowadź dane do formularza');
        }

        if (preg_match('/^[a-zA-ZżźćńółęąśŻŹĆĄŚĘŁÓŃ]+$/', $uName) == 0) {
            throw new ErrorException('Niedozwolone znaki w imieniu');
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
            throw new ErrorException('Wprowadź dane do formularza');
        }

        if (preg_match('/^[0-9]{9}$/', $phone) == 0) {
            throw new ErrorException('Nieprawidłowy numer telefonu');
        }

        return (int) $phone;
    }

    protected function validatePassword(string $pass, string $passR): bool
    {
        //Password validation(5 - 20 characters, minimum of 1 uppercase char, minimum of 1 lowercase char, minimum 1 digit )
        $passValidPattern1 = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{5,20}$/';
        //Passwor only can contain this char
        $passValidPattern2 = '/^[a-zA-Z0-9!@#$%^&*-]+$/';
        if (preg_match($passValidPattern1, $pass) == 0) {
            throw new ErrorException('Podane hasło nie spełnia wymogów!!');
        } elseif (preg_match($passValidPattern2, $pass) == 0) {
            throw new ErrorException('Podane hasło zawiera niedozwolone znaki!!');
        } //checking if both passwords are the same
        elseif ($pass !== $passR) {
            throw new ErrorException('podane hasła nie są takie same!!');
        } else {
            return true;
        }
    }
}
