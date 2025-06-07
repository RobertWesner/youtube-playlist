<?php

namespace App;

use RobertWesner\DependencyInjection\Attributes\AutowireEnv;
use RobertWesner\DependencyInjection\Attributes\BufferFile;

final readonly class ApiKeyProvider
{
    public function __construct(
        #[AutowireEnv(__BASE_DIR__ . '/.env', 'YOUTUBE_API_KEY')]
        #[BufferFile]
        private string $apiKey,
    ) {}

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
