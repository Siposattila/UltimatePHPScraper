<?php

namespace App\Scraper;

use App\Exception\ScraperFailedToLoginException;
use Brick\Browser\Browser;
use Brick\Browser\Client\Client;
use Brick\Browser\RequestHandler\NetworkHandler;

abstract class AbstractScraper extends Browser
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(new NetworkHandler());
        parent::__construct($this->client);
    }

    public abstract function scrape(): void;
    protected abstract function login(): void;

    protected function assertSame(string|array $one, string|array $two): bool
    {
        if (gettype($one) == gettype($two)) {
            if (is_array($one)) {
                return !strcmp(json_encode($one), json_encode($two));
            }

            return $one == $two;
        }

        return false;
    }

    protected function checkLoginSuccess(string $expectedUrl, string $loginUrl): void
    {
        if ($this->assertSame($expectedUrl, $loginUrl)) {
            throw new ScraperFailedToLoginException("Failed to login!");
        }
    }
}
