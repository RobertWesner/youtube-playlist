<?php

use Dotenv\Dotenv;
use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;

require __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Dotenv::createImmutable(__DIR__)->load();

echo RouterFactory::createRouter()->route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
