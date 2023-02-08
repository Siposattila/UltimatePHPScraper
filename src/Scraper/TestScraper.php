<?php

namespace App\Scraper;

use App\Repository\TestRepository;

class TestScraper extends AbstractScraper
{
    private TestRepository $testRepository;

    public function __construct()
    {
        $this->testRepository = new TestRepository();
        parent::__construct();
    }

    public function scrape(): void {}

    protected function login(): void {}

    private function test(): void
    {
        $test = $this->testRepository->queryBuilderTestSelect();
    }
}
