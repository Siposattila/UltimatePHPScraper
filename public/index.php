<?php

use App\Scraper\TestScraper;

require_once __DIR__ . "/../bootstrap.php";

$test = new TestScraper();
$test->testSelect();

//$test->testInsert();
//$test->testInsert();
//$test->testInsert();

// $test->testUpdate();

// $test->testDelete();
