<?php


namespace Healsdata\MastodonListEngagement\Mastodon;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class Api
{
    const ENDPOINT_BOOKMARKS = '/api/v1/bookmarks';
    const ENDPOINT_LISTS = '/api/v1/lists';
    const ENDPOINT_LIST_ACCOUNTS = '/api/v1/lists/:listId/accounts';
    const ENDPOINT_ACCOUNT_FOLLOWS = '/api/v1/accounts/:accountId/follow';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getBookmarks() : ResponseInterface
    {
        return $this->client->get( self::ENDPOINT_BOOKMARKS);
    }

    public function getLists() : ResponseInterface
    {
        return $this->client->get( self::ENDPOINT_LISTS);
    }

    public function createList(string $title, string $repliesPolicy = null) : ResponseInterface
    {
        $jsonData = ['title' => $title];
        if (!empty($repliesPolicy)) {
            $jsonData['replies_policy'] = $repliesPolicy;
        }

        return $this->client->post(self::ENDPOINT_LISTS, ['json' => $jsonData]);
    }

    /**
     * @param int $listId
     * @param array $accountIds
     * @return array|ResponseInterface[]
     * @throws GuzzleException
     */
    public function addAccountsToList(int $listId, array $accountIds) : array
    {
        $url = $this->addParameterToUrl(self::ENDPOINT_LIST_ACCOUNTS, 'listId', $listId);

        $responses = [];

        foreach ($accountIds as $accountId) {
            $jsonData = ['account_ids' => [$accountId]];

            // You must be following an account to add it to a list.
            // @todo this is wholly inefficient. We should bulk check to see who's not followed first.
            $this->followAccount($accountId);

            // In testing, I found a delay between following and being able to add to a list.
            sleep(1);

            try {
                $responses[] = $this->client->post($url, ['json' => $jsonData]);
            } catch (GuzzleException $e) {

                if ($e->getCode() === 422) {
                    // An Account with one of the provided IDs is already in the list
                    // @todo this is wholly inefficient. We should get the account list first.
                    continue;
                }

                print "Failed to add accountId: {$accountId}" . PHP_EOL;
            }
        }

        return $responses;
    }

    public function followAccount(int $accountId) : ResponseInterface
    {
        $url = $this->addParameterToUrl(self::ENDPOINT_ACCOUNT_FOLLOWS, 'accountId', $accountId);
        return $this->client->post($url);
    }


    private function addParameterToUrl(string $url, string $key, string $value) : string
    {
        return str_replace(":{$key}", $value, $url);
    }
}