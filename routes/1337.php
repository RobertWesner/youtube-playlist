<?php

use RobertWesner\SimpleMvcPhp\Route;

(function () {
    $route = /** @lang RegExp */ '(/@vite/env|/actuator/env|/php-cgi.*|/server|/\.vscode/sftp\.json|/debug/default/view\?panel=config|/v2/_catalog|/ecp/Current/exporttool/.*|/login\.action|/_all_dbs|/\.DS_Store|/.env|/\.git/config|/config\.json|/telescope/requests|/info\.php|/\?rest_route=.*)';

    foreach ([
        Route::get(...),
        Route::post(...),
        Route::put(...),
        Route::patch(...),
        Route::delete(...),
    ] as $routing) {
        /** @var callable<Route::get> $routing */
        $routing($route, function () {
            return Route::response('<b>Try harder, you\'re so close!</b>');
        });
    }
})();
