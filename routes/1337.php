<?php

// phpcs:ignoreFile

declare(strict_types=1);

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
        $routing($route, function () {
            switch (random_int(0, 9)) {
                case 0:
                    return Route::response('<b>You\'re so close. Try harder!</b>');
                case 1:
                    return Route::response('<b>Slow down, cowboy!</b>');
                case 2:
                    return Route::response('<b>Wowzers, another blank page?</b>');
                case 3:
                    return Route::response('<b>Get a job</b>');
                case 4:
                    return Route::response('<b>Interesting...</b>');
                case 5:
                    return Route::response('<b>Mate, this website is FOSS anyway. Just read the source code.</b>');
                case 6:
                    return Route::response('<b>Hmmm? Did you say something?</b>', random_int(400, 599));
                case 7:
                    return Route::response('<b>It\'s dangerous to go alone, fuck off!</b>');
                case 8:
                    return Route::response('<b>404? I know what you are doing...</b>', 404);
                case 9:
                    return Route::response('<h1 style="color: red">Internal Server Error</h1><b>But no, it isn\'t... you just tried some stupid things.</b>', 500);
            }
        });
    }
})();
