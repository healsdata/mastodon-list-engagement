<?php

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(getcwd());
$dotEnv->load();
$dotEnv->required(['BEARER_TOKEN','INSTANCE_URL']);

$instanceUrl = $_ENV['INSTANCE_URL'];
$bearerToken = $_ENV['BEARER_TOKEN'];

$endpointBookmarks = '/api/v1/bookmarks';

$accounts = [];

$client = new GuzzleHttp\Client(['base_uri' => $instanceUrl]);

$response = $client->get(
    $endpointBookmarks,
    ['headers' =>
        [
            'Authorization' => "Bearer {$bearerToken}"
        ]
    ]
);
$body = $response->getBody();

$body = json_decode($body, true);

foreach ($body as $toot) {
    $accounts[] = $toot['account']['acct'];
}

print_r($accounts);