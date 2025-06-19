<?php

declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\Configuration;
use RobertWesner\SimpleMvcPhpSpawnerBundle\SpawnerBundle;

Configuration::BUNDLES
    ::load(SpawnerBundle::class);
