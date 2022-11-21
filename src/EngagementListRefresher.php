<?php


namespace Healsdata\MastodonListEngagement;


use Healsdata\MastodonListEngagement\Mastodon\Api;

class EngagementListRefresher
{
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
        $request = $this->api->getBookmarks();
        $json = $request->getBody()->getContents();
        $json = json_decode($json, true);

        $accounts = [];
        foreach ($json as $toot) {
            $accounts = array_merge($accounts, $this->extractor->getAccounts($toot));
        }

        array_map('strtolower', $accounts);
        array_unique($accounts);

        return count($accounts);
    }
}