<?php

declare(strict_types=1);

namespace Market;

use Exception;
use Market\Exception\DatabaseException;
use Market\Exception\ErrorException;
use PDO;
use Throwable;

class GetDatabase extends AbstractDatabase
{
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
