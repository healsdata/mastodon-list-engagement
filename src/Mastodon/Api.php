<?php


namespace Healsdata\MastodonListEngagement\Mastodon;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class Api
{
    const ENDPOINT_BOOKMARKS = '/api/v1/bookmarks';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getBookmarks() : ResponseInterface
    {
        return $this->client->get( self::ENDPOINT_BOOKMARKS);
    }
}