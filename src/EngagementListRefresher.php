<?php


namespace Healsdata\MastodonListEngagement;


use Healsdata\MastodonListEngagement\Mastodon\Api;
use Psr\Http\Message\ResponseInterface;

class EngagementListRefresher
{
    const ENGAGEMENT_LIST_NAME = "Boostin' & Tootin'";

    /**
     * @var Api
     */
    private Api $api;

    /**
     * @var AccountExtractor
     */
    private AccountExtractor $extractor;

    public function __construct(Api $api, AccountExtractor $extractor)
    {
        $this->api = $api;
        $this->extractor = $extractor;
    }

    public function refresh(): int
    {
        $accounts = $this->getEngagementAccounts();
        $listId = $this->getListId();

        $this->api->addAccountsToList($listId, $accounts);

        return count($accounts);
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function getJsonFromResponse(ResponseInterface $response): array
    {
        $json = $response->getBody()->getContents();
        $json = json_decode($json, true);
        return $json;
    }

    /**
     * @return int
     */
    protected function getListId(): int
    {
        $listId = null;
        $response = $this->api->getLists();
        $json = $this->getJsonFromResponse($response);

        foreach ($json as $list) {
            if ($list['title'] == self::ENGAGEMENT_LIST_NAME) {
                $listId = $list['id'];
                break;
            }
        }

        if (is_null($listId)) {
            $response = $this->api->createList(self::ENGAGEMENT_LIST_NAME);
            $json = $this->getJsonFromResponse($response);
            $listId = $json['id'];
        }

        return $listId;
    }

    /**
     * @return array
     */
    protected function getEngagementAccounts(): array
    {
        $response = $this->api->getBookmarks();
        $json = $this->getJsonFromResponse($response);

        $accounts = [];
        foreach ($json as $toot) {
            $accounts = array_merge($accounts, $this->extractor->getAccounts($toot));
        }

        array_map('trim', $accounts);
        array_unique($accounts);

        return $accounts;
    }
}