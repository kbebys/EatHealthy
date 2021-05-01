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

        session_regenerate_id(true);

        $_SESSION['loggedin'] = true;
        $_SESSION['login'] = $login;
        $_SESSION['id'] = $result['id'];

        return true;
    }

    public function getCountAdvertisements(
        string $searchContent,
        int $idPlace,
        string $transaction,
        int $daysBack
    ): int {

        $idPlace = $this->setIdPlaceToQuery($idPlace);
        $transaction = $this->setTypeOfTransactionToQuery($transaction);
        $daysBack = $this->setDaysBackToQuery($daysBack);

        try {
            $query = "SELECT COUNT(*) AS count 
            FROM advertisements AS a
            INNER JOIN user AS u ON a.id_user = u.id
            INNER JOIN user_data AS ud ON u.id = ud.id_user
            WHERE a.title LIKE ?
            $idPlace
            $transaction
            $daysBack";

            $stmt = $this->conn->prepare($query);
            $searchContent = "%" . $searchContent . "%";
            $stmt->bindParam(1, $searchContent, PDO::PARAM_STR);
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
        string $searchContent,
        int $idPlace,
        string $transaction,
        int $daysBack
    ): array {
        $offset = ($pageNumber * $pageSize) - $pageSize;

        $idPlace = $this->setIdPlaceToQuery($idPlace);
        $transaction = $this->setTypeOfTransactionToQuery($transaction);
        $daysBack = $this->setDaysBackToQuery($daysBack);

        try {
            $query =  "SELECT a.id, a.title, p.place, a.date
            FROM user AS u
            INNER JOIN user_data AS ud ON u.id = ud.id_user
            INNER JOIN places AS p ON ud.id_places = p.id
            INNER JOIN advertisements AS a ON u.id = a.id_user
            WHERE a.title LIKE ?
            $idPlace
            $transaction
            $daysBack
            ORDER BY a.date DESC
            LIMIT $offset, $pageSize";

            $stmt = $this->conn->prepare($query);
            $searchContent =  "%" . $searchContent . "%";
            $stmt->bindParam(1, $searchContent, PDO::PARAM_STR);
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
            $query = "SELECT a.title, a.content, p.place, a.date, ud.first_name, ud.phone_number
            FROM user AS u
            INNER JOIN user_data AS ud ON u.id = ud.id_user
            INNER JOIN places AS p ON ud.id_places = p.id
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
            $query =  "SELECT ud.first_name, a.id, a.title, p.place, a.date
            FROM user AS u 
            INNER JOIN user_data AS ud ON u.id = ud.id_user
            INNER JOIN places AS p ON ud.id_places = p.id
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
            $query = "SELECT id, title, content, kind_of_transaction, date
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
            $query = "SELECT ud.first_name AS name, ud.phone_number AS phone, u.email, p.place
            FROM user_data AS ud
            INNER JOIN places AS p ON ud.id_places = p.id
            INNER JOIN user AS u ON ud.id_user = u.id AND u.id = ?";
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

    public function getListOfPlaces(): array
    {
        try {
            $query = "SELECT id, community, place FROM places";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new SubpageValidateException('Błąd pobierania wartości z bazy danych');
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (SubpageValidateException $e) {
            throw new SubpageValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    private function setIdPlaceToQuery(int $idPlace): string
    {
        if ($idPlace === 0) {
            $idPlace = '';
        } else {
            $idPlace = "AND ud.id_places = $idPlace";
        }

        return $idPlace;
    }

    private function setTypeOfTransactionToQuery(string $transaction): string
    {
        $query = "AND a.kind_of_transaction = ";

        if ($transaction === 'all') {
            $transaction = '';
        } else {
            $transaction = $query . "'$transaction'";
        }

        return $transaction;
    }

    private function setDaysBackToQuery(int $daysBack): string
    {
        if ($daysBack === 0) {
            $daysBack = '';
        } else {
            $daysBack = "AND DATEDIFF(NOW(), a.date) <= $daysBack";
        }

        return $daysBack;
    }
}
