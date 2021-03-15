<?php

declare(strict_types=1);

namespace Market\Model;

use Market\Exception\DatabaseException;
use Market\Exception\ErrorException;
use PDO;
use Throwable;

class ReadModel extends AbstractModel
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
            throw new ErrorException('Niepoprawna nazwa użytkownika lub hasło');
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

    public function getUserData(): array
    {
        $id = (int) $_SESSION['id'];

        try {
            $query = "SELECT user_data.first_name AS name, user_data.phone_number AS phone, user.email AS email
            FROM user_data
            INNER JOIN user ON user_data.id_user = user.id AND user.id = ?";
            $stmt = self::$conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                $result = [];
            } else {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return $result;
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }


    public function checkPassword(string $password): bool
    {
        $password = trim($password);

        if (empty($password)) {
            throw new ErrorException('Wprowadź hasło');
        }

        $id = (int) $_SESSION['id'];
        $stmt = $this->checkUser('id', $id);

        // if (!$stmt) {
        //     throw new Exception('Problem z połączeniem z bazą danych ');
        // }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $result['password'])) {
            throw new ErrorException('Podałeś nieprawidłowe hasło');
        }

        return true;
    }
}
