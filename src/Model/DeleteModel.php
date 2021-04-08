<?php

declare(strict_types=1);

namespace Market\Model;

use Error;
use Exception;
use Market\Exception\DatabaseException;
use Market\Exception\PageValidateException;
use Market\Exception\SubpageValidateException;
use PDO;
use Throwable;

class DeleteModel extends AbstractModel
{
    public function deleteAcc(): void
    {
        try {
            $id = (int) $_SESSION['id'];

            $query = "DELETE FROM user WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() !== 1) {
                throw new PageValidateException('Błąd usuwania konta');
            }
        } catch (PageValidateException $e) {
            throw new PageValidateException($e->getMessage());
        } catch (Exception $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    public function deleteUserAdvertisment(int $idAdv): bool
    {
        $id = (int) $_SESSION['id'];

        try {
            $query = "DELETE FROM advertisment WHERE id = ? AND id_user = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $idAdv, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                return true;
            } else {
                throw new SubpageValidateException('Nie znaleziono ogłoszenia o takim id');
            }
        } catch (SubpageValidateException $e) {
            throw new SubpageValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }
}
