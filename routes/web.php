<?php

declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\Route;

$server = [
    'uri' => $_SERVER['REQUEST_URI'],
];

Route::get('/', fn () => Route::render('index.twig', ['server' => $server]));
Route::get('/(data|privacy|contact)/?', fn () => Route::render('data.twig', ['server' => $server]));
