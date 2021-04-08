<?php

declare(strict_types=1);

namespace Market\Model;

use Exception;
use Market\Exception\DatabaseException;
use Market\Exception\PageValidateException;
use Market\Exception\SubpageValidateException;
use Market\Exception\ValidateException;
use PDO;
use Throwable;

class UpdateModel extends AbstractModel
{
    //Changing user first name
    public function changeName(string $uName): bool
    {
        $uName = $this->validateName($uName);

        if (!$uName) {
            throw new ('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $id = (int) $_SESSION['id'];

        try {
            $query = "UPDATE user_data SET first_name = ? WHERE id_user = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $uName, PDO::PARAM_STR);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new ValidateException('Problem ze zmianą wartości w bazie danych');
            }

            return true;
        } catch (ValidateException $e) {
            throw new ValidateException($e->getMessage());
        } catch (Exception $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    //Changing user phone number
    public function changePhone(string $phone): bool
    {
        $phone = $this->validatePhone($phone);

        if (!$phone) {
            throw new SubpageValidateException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }

        $id = (int) $_SESSION['id'];

        try {
            $query = "UPDATE user_data SET phone_number = ? WHERE id_user = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $phone, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new SubpageValidateException('Problem ze zmianą wartości w bazie danych');
            }

            return true;
        } catch (SubpageValidateException $e) {
            throw new SubpageValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    //Changing password
    public function changePassword(array $data): bool
    {
        $data = array_map('trim', $data);

        if ($this->validateEmpty($data)) {
            throw new PageValidateException('Wprowadź dane');
        }

        $old = $data['old'];
        $new = $data['new'];
        $newRepeat = $data['newRepeat'];
        $id = (int) $_SESSION['id'];

        if ($old === $new) {
            throw new PageValidateException('Nowe hasło musi być inne od aktualnego');
        }


        $stmt = $this->checkUserExist('id', $id);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!password_verify($old, $result['password'])) {
            throw new PageValidateException('Niepoprawne aktualne hasło');
        }

        $passValid = $this->validatePasswords($new, $newRepeat);
        if ($passValid !== true) {
            throw new PageValidateException('Problem z odczytaniem wartości. Spróbuj jeszce raz');
        }
        $passwordHashed = password_hash($new, PASSWORD_DEFAULT);
        try {
            $query = "UPDATE user SET password = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $passwordHashed, PDO::PARAM_STR);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new PageValidateException('Problem ze zmianą wartości w bazie danych');
            }

            return true;
        } catch (PageValidateException $e) {
            throw new PageValidateException($e->getMessage());
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
    }

    //Changing data of advertisment
    public function changeAdvertisment(array $advData, int $idAdv): bool
    {
        $advData = array_map('trim', $advData);

        if ($this->validateEmpty($advData)) {
            throw new SubpageValidateException('Uzupełnij wszytkie pola', 2);
        }

        $title = $advData['title'];
        $kind = $advData['kind'];
        $content = $advData['content'];
        $place = $advData['place'];
        $id = (int) $_SESSION['id'];

        if (strlen($title) > 150) {
            throw new SubpageValidateException('Wpisałeś za długi tytuł', 2);
        }

        if ($kind !== 'sell' && $kind !== 'buy') {
            throw new SubpageValidateException('Błąd wysyłania danych. Spróbuj jeszce raz', 2);
        }

        if (strlen($place) > 150) {
            throw new SubpageValidateException('Wpisałeś za długi tytuł', 2);
        }

        if ($this->checkAdvertismentExist($id, $idAdv) === false) {
            throw new SubpageValidateException('Nie znaleziono ogłoszenia o takim id', 2);
        }

        try {
            $query = "UPDATE advertisment
                SET title = ?, content = ?, kind_of_transaction = ?, place = ? 
                WHERE id = ? AND id_user = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $title, PDO::PARAM_STR);
            $stmt->bindParam(2, $content, PDO::PARAM_STR);
            $stmt->bindParam(3, $kind, PDO::PARAM_STR);
            $stmt->bindParam(4, $place, PDO::PARAM_STR);
            $stmt->bindParam(5, $idAdv, PDO::PARAM_INT);
            $stmt->bindParam(6, $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                return true;
            } else {
                throw new SubpageValidateException('Nie edytowałeś danych w ogłoszeniu');
            }
        } catch (SubpageValidateException $e) {
            throw new SubpageValidateException($e->getMessage(), 2);
        } catch (Throwable $e) {
            throw new DatabaseException('Problem z połączeniem z bazą danych ', 400, $e);
        }
        return true;
    }
}
