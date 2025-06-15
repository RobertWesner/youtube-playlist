<?php

/**
 * @version v0.10.0
 */

declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\MVC;

require __DIR__ . '/vendor/autoload.php';

const __BASE_DIR__ = __DIR__;

// replace with proper logging in the future
file_put_contents('php://stderr', sprintf(
    '[%s] Request on "%s" by "%s".' . "\n",
    date('Y-m-d h:i:s'),
    $_SERVER['REQUEST_URI'],
    $_SERVER['HTTP_USER_AGENT'],
));

MVC::route();
