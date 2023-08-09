<?php
include '../src/config.php';
include '../vendor/autoload.php';

$route = new \Base\Route();


$app = new \Base\Application();
$app->run();
