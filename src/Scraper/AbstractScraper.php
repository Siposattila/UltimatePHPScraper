<?php

namespace App\Scraper;

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
}
