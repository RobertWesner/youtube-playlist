<?php

declare(strict_types=1);

use App\ApiKeyProvider;
use App\PlaylistCache;
use App\PlaylistService;
use App\Spawn\Fetch\FetchSpawn;
use App\Spawn\Fetch\FetchSpawnConfiguration;
use Google\Client;
use Google\Service\Exception as GoogleException;
use Google\Service\YouTube;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;
use RobertWesner\SimpleMvcPhpSpawnerBundle\Spawner\Spawner;

Route::post('/api/list', function (Request $request, ApiKeyProvider $apiKeyProvider, Spawner $spawner) {
    $uri = $request->getRequestParameter('uri');
    if ($uri === null) {
        return Route::json([
            'status' => 'bad-request',
        ], 400);
    }

    $requestType = $request->getRequestParameter('requestType', 'unknown');

    file_put_contents('php://stderr', sprintf(
        '[%s] (%s) Attempting to fetch URI "%s".' . "\n",
        date('Y-m-d H:i:s'),
        $requestType,
        $uri,
    ));

    $list = null;
    $cache = PlaylistCache::getInstance()->getCache();
    try {
        $client = new Client();
        $client->setDeveloperKey($apiKeyProvider->getApiKey());
        $youtube = new YouTube($client);

        $matches = [];
        if (preg_match('/https:\/\/(?:m|www)\.youtube\.com\/(@[^\/?#]+).*/', $uri, $matches)) {
            $list = 'UULF' . substr(
                $youtube->channels->listChannels(
                    'id',
                    ['forHandle' => $matches[1]]
                )->getItems()[0]['id'],
                2
            );
        } elseif (preg_match('/https:\/\/(?:m|www)\.youtube\.com\/playlist.*?(?:\?|&)list=([^&]+)/', $uri, $matches)) {
            $list = $matches[1];
        } else {
            // replace with proper logging in the future
            file_put_contents('php://stderr', sprintf(
                '[%s] (%s) Invalid URI "%s".' . "\n",
                date('Y-m-d H:i:s'),
                $requestType,
                $uri,
            ));

            return Route::json([
                'status' => 'bad-request',
            ], 400);
        }

        // prevent private lists like WL
        if (strlen($list) <= 4) {
            // replace with proper logging in the future
            file_put_contents('php://stderr', sprintf(
                '[%s] (%s) Attempted access to short playlist "%s".' . "\n",
                date('Y-m-d H:i:s'),
                $requestType,
                $uri,
            ));

            return Route::json([
                'status' => 'fetched',
                'uri' => $uri,
                'items' => [],
            ]);
        }

        $cached = $cache->get($list);
        // Cache for 10 minutes to prevent exhausting the API key
        if ($cached !== false && $cached['time'] >= (int)gmdate('U') - 600) {
            return Route::json([
                'status' => 'cached',
                'uri' => $uri,
                'items' => $cached['items'],
            ]);
        }
        $cache->set($list, [
            'time' => (int)gmdate('U'),
            'items' => [],
        ]);

        $videoCount = ($youtube->playlists->listPlaylists('contentDetails', [
            'id' => $list,
        ])->getItems()[0] ?? null)?->getContentDetails()?->getItemCount();
        if ($videoCount === null) {
            // replace with proper logging in the future
            file_put_contents('php://stderr', sprintf(
                '[%s] (%s) Missing or private playlist? Attempted to fetch "%s".' . "\n",
                date('Y-m-d H:i:s'),
                $requestType,
                $uri,
            ));

            return Route::json([
                'status' => 'bad-request',
            ], 400);
        }

        if ($videoCount >= 1000) {
            $spawner->spawn(FetchSpawn::class, new FetchSpawnConfiguration($list));

            return Route::json([
                'status' => 'running',
            ], 202);
        }

        $items = PlaylistService::getItems($youtube, $list);
    } catch (GoogleException) {
        if ($list !== null) {
            $cache->delete($list);
        }

        // replace with proper logging in the future
        file_put_contents('php://stderr', sprintf(
            '[%s] (%s) Quota exceeded? Attempted to fetch "%s".' . "\n",
            date('Y-m-d H:i:s'),
            $requestType,
            $uri,
        ));

        return Route::json([
            'status' => 'error',
        ], 500);
    }

    $cache->set($list, [
        'time' => (int)gmdate('U'),
        'items' => $items,
    ]);

    return Route::json([
        'status' => 'fetched',
        'uri' => $uri,
        'items' => $items,
    ]);
});
