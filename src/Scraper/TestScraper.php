<?php

namespace App\Scraper;

use App\Entity\Test;
use App\Repository\TestRepository;

class TestScraper extends AbstractScraper
{
    private TestRepository $testRepository;

    public function __construct()
    {
        $this->testRepository = new TestRepository();
        parent::__construct();
    }

    public function scrape(): void
    {
    }

    protected function login(): void
    {
    }

    // TODO: Turn these into tests with phpunit
    public function testSelect(): void
    {
        $test = $this->testRepository->queryBuilderTestSelect();
        var_dump($test);
    }

    public function testInsert(): void
    {
        $new = new Test();
        $new->setAge(random_int(0, 30));
        $new->setName("Test " . uniqid("test"));
        $new->setYear(random_int(1970, 2023));

        $this->testRepository->save($new);
    }

    public function testUpdate(): void
    {
        $update = $this->testRepository->find(2);
        $update->setYear(random_int(1900, 1970));

        $this->testRepository->save($update);
    }

    public function testDelete(): void
    {
        $this->testRepository->delete($this->testRepository->find(2));
    }
}
