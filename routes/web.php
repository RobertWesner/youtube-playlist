<?php

declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\Route;

$server = [
    'uri' => $_SERVER['REQUEST_URI'],
];

Route::get('/', function () use ($server) {
    return Route::render('index.twig', ['server' => $server]);
});
