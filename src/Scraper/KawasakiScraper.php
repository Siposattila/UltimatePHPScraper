<?php

namespace App\Scraper;

class KawasakiScraper extends AbstractScraper
{
    public function scrape(): void
    {
        $this->login();
        // TODO: implement
    }

    protected function login(): void
    {
        // TODO: throw exception :) if not logged in and implement
    }

    public function test(): void
    {
        $this->open($_ENV["KAWASAKI_BASE_URL"]);
        var_dump($this->getContent());
    }
}
