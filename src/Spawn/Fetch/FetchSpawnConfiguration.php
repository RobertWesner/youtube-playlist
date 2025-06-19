<?php

declare(strict_types=1);

namespace App\Spawn\Fetch;

use RobertWesner\SimpleMvcPhpSpawnerBundle\Spawner\SpawnConfigurationInterface;

class FetchSpawnConfiguration implements SpawnConfigurationInterface
{
    public function __construct(
        private string $playlist,
    ) {}

    public function __serialize(): array
    {
        return [
            'playlist' => $this->playlist,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->playlist = $data['playlist'];
    }

    public function getPlaylist(): string
    {
        return $this->playlist;
    }
}
