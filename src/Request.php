<?php

declare(strict_types=1);

namespace Market;


class Request
{
    private array $get;
    private array $post;

    public function __construct(array $get, array $post)
    {
        $this->get = $get;
        $this->post = $post;
    }

    public function getParam(string $name, $default = null): ?string
    {
        return $this->get[$name] ?? $default;
    }

    public function postParam(string $name, $default = false)
    {
        return $this->post[$name] ?? $default;
    }
}
