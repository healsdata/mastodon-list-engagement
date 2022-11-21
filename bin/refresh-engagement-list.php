<?php

use Healsdata\MastodonListEngagement\AccountExtractor;
use Healsdata\MastodonListEngagement\EngagementListRefresher;
use Healsdata\MastodonListEngagement\Mastodon\Api;
use Psr\Container\ContainerInterface;

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(getcwd());
$dotEnv->load();

/** @var ContainerInterface $container */
$container = require_once 'config/dependencies.php';

/** @var EngagementListRefresher $engagementListRefresher */
$engagementListRefresher = $container->get(EngagementListRefresher::class);

$accountCount = $engagementListRefresher->refresh();

print $accountCount . " accounts found and added to the engagement list";