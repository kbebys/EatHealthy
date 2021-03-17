<?php

declare(strict_types=1);

namespace Market\Model;

use Exception;
use Market\Exception\DatabaseException;
use Market\Exception\ErrorException;
use PDO;
use Throwable;

class UpdateModel extends AbstractModel
{
    //Changing user first name
    public function changeName(string $uName): bool
    {
        $uName = $this->validateName($uName);

        if (!$uName) {
            throw new Exception('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $id = (int) $_SESSION['id'];


        try {
            $query = "UPDATE user_data SET first_name = ? WHERE id_user = ?";
            $stmt = self::$conn->prepare($query);
            $stmt->bindParam(1, $uName, PDO::PARAM_STR);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (Exception $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    //Changing user phone number
    public function changePhone(string $phone): bool
    {
        $phone = $this->validatePhone($phone);

        if (!$phone) {
            throw new ErrorException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $id = (int) $_SESSION['id'];

        try {
            $query = "UPDATE user_data SET phone_number = ? WHERE id_user = ?";
            $stmt = self::$conn->prepare($query);
            $stmt->bindParam(1, $phone, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    //Changing password
    public function changePassword(array $data): bool
    {
        $data = array_map('trim', $data);

        if ($this->validateEmpty($data)) {
            throw new ErrorException('Wprowadź dane');
        }

        $old = $data['old'];
        $new = $data['new'];
        $newRepeat = $data['newRepeat'];
        $id = (int) $_SESSION['id'];

        if ($old === $new) {
            throw new ErrorException('Nowe hasło musi być inne od aktualnego');
        }


        $stmt = $this->checkUser('id', $id);

        // if (!$stmt) {
        //     throw new Exception("Błąd");
        // }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!password_verify($old, $result['password'])) {
            throw new ErrorException('Niepoprawne aktualne hasło');
        }

        $passValid = $this->validatePassword($new, $newRepeat);
        if ($passValid !== true) {
            throw new ErrorException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $passwordHashed = password_hash($new, PASSWORD_DEFAULT);
        try {
            $query = "UPDATE user SET password = ? WHERE id = ?";
            $stmt = self::$conn->prepare($query);
            $stmt->bindParam(1, $passwordHashed, PDO::PARAM_STR);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (Exception $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }
}
