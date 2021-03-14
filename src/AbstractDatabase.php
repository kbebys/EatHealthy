<?php

declare(strict_types=1);

namespace Market;

use Exception;
use Market\Exception\DatabaseException;
use PDO;
use PDOException;
use Throwable;

abstract class AbstractDatabase
{
    protected PDO $conn;

    public function __construct(array $config)
    {
        try {
            $this->validateConfig($config);
            $this->createConneciton($config);
        } catch (PDOException $e) {
            throw new Exception('Błąd połączenia');
        }
    }

    private function createConneciton(array $config): void
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']}";

        $this->conn = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [ //Throw PDOException by default]
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

    protected function checkUser(string $param, $user)
    {
        try {
            $query = "SELECT id, password FROM user WHERE $param = ?";
            $stmt = $this->conn->prepare($query);
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
}
