<?php

use Hen\App;
use Hen\Exception\MissingParamException;

require_once implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'bootstrap', 'autoload.php']);

if ($argc < 4) {
    App::get()->logger->error('In job-worker, missing params.', [
        'argc' => $argc,
        'argv' => $argv,
    ]);
    throw new MissingParamException();
}

$appName = $argv[1];
$username = $argv[2];
$password = $argv[3];

App::get()->platform($appName)
    ->setUsername($username)
    ->setPassword($password)
    ->sign();