<?php

declare(strict_types=1);

namespace Market;

class View
{
    public function render(string $page, string $subpage = '', array $params = []): void
    {
        $params = $this->escape($params);

        $params['access'] = true;
        $params['wiadomosc'] = 'jestem w view';
        dump($params);
        require_once('templates/layout.php');
    }

    //Function uses to escaping data sent by $params array
    private function escape(array $params): array
    {
        $escapeParams = [];

        foreach ($params as $key => $value) {

            switch (true) {
                case is_array($value):
                    $escapeParams[$key] = $this->escape($value);
                    break;
                case is_int($value) || is_bool($value):
                    $escapeParams[$key] = $value;
                    break;
                case $value:
                    $escapeParams[$key] = htmlentities($value);
                    break;
            }
        }

        return $escapeParams;
    }
}
