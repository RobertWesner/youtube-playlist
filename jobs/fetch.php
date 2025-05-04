<?php

use App\PlaylistCache;
use App\PlaylistService;
use Dotenv\Dotenv;
use Google\Client;
use Google\Service\YouTube;

require __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[1])) {
    die('no playlist passed!');
}

$list = $argv[1];
Dotenv::createImmutable(__DIR__ . '/..')->load();

$client = new Client();
$client->setDeveloperKey($_ENV['YOUTUBE_API_KEY']);
$youtube = new YouTube($client);
$cache = PlaylistCache::getInstance()->getCache();

$cache->set($list, [
    'time' => (int)gmdate('U'),
    'items' => [],
]);
PlaylistService::getItems($youtube, $list, function (array $items) use ($cache, $list) {
    $cache->set($list, [
        'time' => (int)gmdate('U'),
        'items' => $items,
    ]);
});
