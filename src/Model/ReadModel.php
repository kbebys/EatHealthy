<?php

declare(strict_types=1);

namespace Market\Model;

use Market\Exception\DatabaseException;
use Market\Exception\PageValidateException;
use Market\Exception\SubpageValidateException;
use Market\Exception\ValidateException;
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
            throw new ValidateException('Wprowadź dane logowania');
        }

        $login = $data['login'];
        $password = $data['password'];

        $stmt = $this->checkUserExist('login', $login);
        if (!$stmt) {
            throw new ValidateException('Niepoprawna nazwa użytkownika lub hasło');
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //veryfing passwords, both given from user and hashed from db
        if (!password_verify($password, $result['password'])) {
            throw new ValidateException('Niepoprawna nazwa użytkownika lub hasło');
        }

        //Creating session to know the user is logged in
        session_regenerate_id();

        $_SESSION['loggedin'] = true;
        $_SESSION['name'] = $login;
        $_SESSION['id'] = $result['id'];

        return true;
    }

    public function getCountAdvertisements(string $searchContent = ''): int
    {
        if ($searchContent) {
            $searchContent = "WHERE title LIKE '%" . $searchContent . "%'";
        }

        try {
            $query = "SELECT COUNT(*) AS count 
            FROM advertisements
            " . $searchContent . "";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) $count['count'];
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function getAdvertisements(
        int $pageNumber,
        int $pageSize,
        string $searchContent
    ): array {
        $offset = ($pageNumber * $pageSize) - $pageSize;

        if ($searchContent) {
            $searchContent = "WHERE a.title LIKE '%" . $searchContent . "%'";
        }
        dump($searchContent);

        try {
            $query =  "SELECT a.id, a.title, a.place, a.date
            FROM user AS u
            INNER JOIN user_data AS ud ON u.id = ud.id_user
            INNER JOIN advertisements AS a ON u.id = a.id_user
            " . $searchContent . "
            ORDER BY a.date DESC
            LIMIT $offset, $pageSize";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function getAdvertisement(int $idAdvert): array
    {
        try {
            $query = "SELECT a.title, a.content, a.place, a.date, ud.first_name, ud.phone_number
            FROM user AS u
            INNER JOIN user_data AS ud ON u.id = ud.id_user
            INNER JOIN advertisements AS a ON u.id = a.id_user
            WHERE a.id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $idAdvert, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new ValidateException('Nie można odnaleźć ogłoszenia o takim id');
            }

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;
        } catch (ValidateException $e) {
            throw new ValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function getUserAdvertisements(int $pageNumber = 1, int $pageSize = 20): array
    {
        $offset = ($pageNumber * $pageSize) - $pageSize;
        $id = (int) $_SESSION['id'];
        try {
            $query =  "SELECT ud.first_name, a.id, a.title, a.place, a.date
            FROM user AS u 
            INNER JOIN user_data AS ud ON u.id = ud.id_user
            INNER JOIN advertisements AS a ON u.id = a.id_user
            WHERE u.id = ?
            ORDER BY a.date DESC
            LIMIT $offset, $pageSize";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new SubpageValidateException('Błąd pobierania ogłoszeń');
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (SubpageValidateException $e) {
            throw new SubpageValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function getCountUserAdvertisements(): int
    {
        $id = (int) $_SESSION['id'];
        try {
            $query =  "SELECT count(*) AS count
            FROM user AS u 
            INNER JOIN user_data AS ud ON u.id = ud.id_user
            INNER JOIN advertisements AS a ON u.id = a.id_user
            WHERE u.id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) $count['count'];
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function getUserAdvertisement(int $idAdv): array
    {
        $id = (int) $_SESSION['id'];

        try {
            $query = "SELECT id, title, content, place, kind_of_transaction, date
            FROM advertisements
            WHERE id = ? and id_user = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $idAdv, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new SubpageValidateException('Nie można odnaleźć ogłoszenia o takim id');
            }

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;
        } catch (SubpageValidateException $e) {
            throw new SubpageValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function getUserData(): array
    {
        $id = (int) $_SESSION['id'];
        try {
            $query = "SELECT user_data.first_name AS name, user_data.phone_number AS phone, user.email AS email
            FROM user_data
            INNER JOIN user ON user_data.id_user = user.id AND user.id = ?";
            $stmt = $this->conn->prepare($query);
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
            throw new PageValidateException('Wprowadź hasło');
        }

        $id = (int) $_SESSION['id'];
        $stmt = $this->checkUserExist('id', $id);
        if (!$stmt) {
            throw new PageValidateException('Problem z pobraniem hasła');
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $result['password'])) {
            throw new PageValidateException('Podałeś nieprawidłowe hasło');
        }

        return true;
    }

    public function getPlaces(): array
    {
        try {
            $query = "SELECT DISTINCT place FROM advertisements";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

            return $result;
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }
}
