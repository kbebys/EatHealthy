<?php

declare(strict_types=1);

namespace Market\ErrorHandler;

use DateTime;
use Throwable;

class ErrorHandler
{
    public function errorLog(Throwable $error, string $erorrKind): void
    {
        $message = $this->setErrorMessage($error);

        $dir = str_replace(['\\', 'src/ErrorHandler'], ['/', ''], __DIR__);
        $file = $erorrKind . '.log';
        $path = $dir . 'files/errors/' . $file;
        if (!error_log($message, 3, $path)) {
            error_log('Problem z funkcją error_log()', 1, 'mail@example.com', 'Subject: EatHealthy logError\nFrom: example@mail.com\n');
        }
    }

    private function setErrorMessage(Throwable $error): string
    {
        if (is_object($error->getPrevious())) {
            $message = $this->setErrorMessage($error->getPrevious());
        } else {
            $currentTime = (new DateTime('now'))->format('Y-m-d H:i:s');
            $message = "Message: " . $error->getMessage() . "\n File: " . $error->getFile() . "\n Line: " . $error->getLine()  . "\n Code: " . $error->getCode() . "\n Time: " . $currentTime . "\n\n";
        }

        return $message;
    }
}
