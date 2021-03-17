<?php

declare(strict_types=1);

namespace Market\Model;

use Exception;
use Market\Exception\DatabaseException;
use PDO;

class DeleteModel extends AbstractModel
{
    public function deleteAcc(): void
    {
        try {
            $id = (int) $_SESSION['id'];

            $query = "DELETE FROM user WHERE id = ? LIMIT 1";
            $stmt = self::$conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }
}
