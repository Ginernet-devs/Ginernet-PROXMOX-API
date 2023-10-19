<?php

use Symfony\Component\Dotenv\Dotenv;

const DIR_VENDOR = __DIR__ . '/../vendor/';

if (file_exists(DIR_VENDOR . 'autoload.php')) {
    require_once(DIR_VENDOR . 'autoload.php');
}else{
    var_dump("ERROR");
}

$dotenv = new Dotenv();
$file = __DIR__. '/.env.test';
$dotenv->load($file);