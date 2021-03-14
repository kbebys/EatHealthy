<?php

declare(strict_types=1);

//Enable displaying all errors
error_reporting(E_ALL);
ini_set('display_errors', '1');

function dump($data)
{
    echo '<br>
    <div style="
    display: inline-block;
    padding: 5px 10px;
    border: solid 2px black;
    background-color: grey;">
    <pre>';
    print_r($data);
    echo '</pre>
    </div>
    <br>';
}
