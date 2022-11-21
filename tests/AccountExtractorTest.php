<?php

namespace Healsdata\MastodonListEngagement\Test;

use Healsdata\MastodonListEngagement\AccountExtractor;
use PHPUnit\Framework\TestCase;

class AccountExtractorTest extends TestCase
{

    private AccountExtractor $extractor;

    public function setUp(): void
    {
        $this->extractor = new AccountExtractor();
    }

    public function testReturnsTootAccount()
    {
        $json = file_get_contents('data/example-toot-bookmark.json');
        $json = json_decode($json, true);

        $this->assertEquals(
            ['evanphx@ruby.social'],
            $this->extractor->getAccounts($json)
        );
    }
}
