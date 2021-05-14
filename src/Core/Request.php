<?php

declare(strict_types=1);

namespace Market\Core;


class Request
{
    private array $get;
    private array $post;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
    }

    public function getParam(string $name, $default = null)
    {
        return $this->get[$name] ?? $default;
    }

    public function postParam(string $name, $default = false)
    {
        return $this->post[$name] ?? $default;
    }

    public function getClassesList(string $dir): array
    {
        $dir = __DIR__ . $dir;
        $filesName = scandir($dir);

        foreach ($filesName as $value) {
            if ($value !== '.' && $value !== '..') {
                $filesList[] = str_replace('.php', '', $value);
            }
        }

        return $filesList ?? [];
    }
}
