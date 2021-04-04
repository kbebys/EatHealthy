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

    public function getParam(string $name, $default = null): ?string
    {
        return $this->get[$name] ?? $default;
    }

    public function postParam(string $name, $default = false)
    {
        return $this->post[$name] ?? $default;
    }

    public function getPagesList(): array
    {
        $dir = __DIR__ . '\..\..\templates\pages';
        $filesNames = scandir($dir);

        foreach ($filesNames as $key => $value) {

            if ($value !== '.' && $value !== '..' && $value !== 'subpages') {
                $filesList[] = str_replace('.php', '', $value);
            }
        }

        return $filesList ?? [];
    }
}
