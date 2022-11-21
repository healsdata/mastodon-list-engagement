<?php

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

$requiredEnvVariables = ['BEARER_TOKEN', 'INSTANCE_URL'];

if ($dotEnv instanceof Dotenv) {
    $dotEnv->required($requiredEnvVariables);
}

$config = [
    Client::class => function (ContainerInterface $c) {
        return new GuzzleHttp\Client(
            [
                'base_uri' => $_ENV['INSTANCE_URL'],
                'headers' => [
                    'Authorization' => "Bearer " . $_ENV['BEARER_TOKEN']
                ]
            ]
        );
    },
];

$builder = new DI\ContainerBuilder();
$builder->addDefinitions($config);
$container = $builder->build();

return $container;