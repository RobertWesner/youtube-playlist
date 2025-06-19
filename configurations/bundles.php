<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use RobertWesner\SimpleMvcPhp\Configuration;
use RobertWesner\SimpleMvcPhpMonologBundle\MonologBundle;
use RobertWesner\SimpleMvcPhpMonologBundle\MonologBundleConfiguration;
use RobertWesner\SimpleMvcPhpSpawnerBundle\SpawnerBundle;

Configuration::BUNDLES
    ::load(SpawnerBundle::class)
    ::load(
        MonologBundle::class,
        new MonologBundleConfiguration(
            'ytplaylist',
            [new StreamHandler('php://stdout')],
        ),
    );
