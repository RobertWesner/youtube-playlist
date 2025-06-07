<?php

namespace App\Spawn\Fetch;

use App\ApiKeyProvider;
use App\PlaylistCache;
use App\PlaylistService;
use Google\Client;
use Google\Service\YouTube;
use RobertWesner\SimpleMvcPhpSpawnerBundle\Spawner\SpawnConfigurationInterface;
use RobertWesner\SimpleMvcPhpSpawnerBundle\Spawner\SpawnInterface;

final readonly class FetchSpawn implements SpawnInterface
{
    public function __construct(
        private ApiKeyProvider $apiKeyProvider,
    ) {}

    public function run(SpawnConfigurationInterface|FetchSpawnConfiguration $configuration): void
    {
        $list = $configuration->getPlaylist();

        $client = new Client();
        $client->setDeveloperKey($this->apiKeyProvider->getApiKey());
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
    }
}
