<?php

declare(strict_types=1);

namespace App;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use MatthiasMullie\Scrapbook\Adapters\Flysystem;

final class PlaylistCache
{
    private const string CACHE_DIR = '/var/cache/ytplaylist';

    private static self $instance;
    private Flysystem $cache;

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getCache(): Flysystem
    {
        return $this->cache;
    }

    private function __construct()
    {
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, recursive: true);
        }

        // clean up old caches
        foreach (array_diff(scandir(self::CACHE_DIR), ['.', '..']) as $file) {
            $file = self::CACHE_DIR . '/' . $file;
            if (filemtime($file) < strtotime('-30 min')) {
                unlink($file);
            }
        }

        $adapter = new LocalFilesystemAdapter(self::CACHE_DIR, null, LOCK_EX);
        $filesystem = new Filesystem($adapter);
        $this->cache = new Flysystem($filesystem);
    }
}
