<?php

// phpcs:ignoreFile

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use RobertWesner\SimpleMvcPhp\Route;

(function () {
    $route = /** @lang RegExp */ '(/@vite/env|/actuator/env|/php-cgi.*|/server|/\.vscode/sftp\.json|/debug/default/view\?panel=config|/v2/_catalog|/ecp/Current/exporttool/.*|/login\.action|/_all_dbs|/\.DS_Store|/.env|/\.git/config|/config\.json|/telescope/requests|/info\.php|/\?rest_route=.*|/wordpress/.*|/blog/.*|/web/.*|/website/.*|/news/.*|/20\d{2}/.*|/shop/.*|/wp\d/.*|/test/.*|/media/.*|/cms/.*|/sito/.*)';

    foreach (
        [
            Route::get(...),
            Route::post(...),
            Route::put(...),
            Route::patch(...),
            Route::delete(...),
        ] as $routing
    ) {
        /** @var callable<Route::get> $routing */
        $routing($route, function (LoggerInterface $logger) {
            $logger->debug(sprintf(
                'Goofy goofball attempted %s request on "%s" by "%s".',
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                $_SERVER['HTTP_USER_AGENT'],
            ));

            return match (random_int(0, 9)) {
                0 => Route::response('<b>You\'re so close. Try harder!</b>'),
                1 => Route::response('<b>Slow down, cowboy!</b>'),
                2 => Route::response('<b>Wowzers, another blank page?</b>'),
                3 => Route::response('<b>Get a job</b>'),
                4 => Route::response('<b>Interesting...</b>'),
                5 => Route::response('<b>Mate, this website is FOSS anyway. Just read the source code.</b>'),
                6 => Route::response('<b>Hmmm? Did you say something?</b>', random_int(400, 599)),
                7 => Route::response('<b>It\'s dangerous to go alone, fuck off!</b>'),
                8 => Route::response('<b>404? I know what you are doing...</b>', 404),
                9 => Route::response('<h1 style="color: red">Internal Server Error</h1><b>But no, it isn\'t... you just tried some stupid things.</b>', 500),
            };
        });

        $routing(Route::FALLBACK, function (LoggerInterface $logger) {
            $logger->info(sprintf(
                '%s request on "%s" by "%s".',
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                $_SERVER['HTTP_USER_AGENT'],
            ));

            return Route::response('Not found', 404);
        });
    }
})();
