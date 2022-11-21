<?php


namespace Healsdata\MastodonListEngagement;


class AccountExtractor
{

    public function getAccounts(array $tootJson) : array
    {
        $accounts = [];
        $accounts[] = $tootJson['account']['acct'];
        return $accounts;
    }
}