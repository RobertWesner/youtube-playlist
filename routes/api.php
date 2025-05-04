<?php

use App\PlaylistCache;
use App\PlaylistService;
use Google\Client;
use Google\Service\YouTube;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;

Route::post('/api/list', function (Request $request) {
    $uri = $request->getRequestParameter('uri');

    $client = new Client();
    $client->setDeveloperKey($_ENV['YOUTUBE_API_KEY']);
    $youtube = new YouTube($client);

    $list = null;
    $matches = [];
    if (preg_match('/https:\/\/(?:m|www)\.youtube\.com\/(@[^\/?#]+).*/', $uri, $matches)) {
        $list = 'UULF' . substr($youtube->channels->listChannels('id', ['forHandle' => $matches[1]])->getItems()[0]['id'], 2);
    } elseif (preg_match('/https:\/\/(?:m|www)\.youtube\.com\/playlist.*?(?:\?|&)list=([^&]+)/', $uri, $matches)) {
        $list = $matches[1];
    }

    $cache = PlaylistCache::getInstance()->getCache();
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

    $videoCount = $youtube->playlists->listPlaylists('contentDetails', [
        'id' => $list,
    ])->getItems()[0]->getContentDetails()->getItemCount();
    if ($videoCount >= 1000) {
        $_ = [];
        proc_close(proc_open('php ' . __DIR__ . '/../jobs/fetch.php ' . escapeshellarg($list) . ' &', [], $_));

        return Route::json([
            'status' => 'running',
        ], 202);
    }

    $items = PlaylistService::getItems($youtube, $list);
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
