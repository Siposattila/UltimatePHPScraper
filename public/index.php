<?php

use App\Scraper\KawasakiScraper;

require_once __DIR__ . "/../bootstrap.php";

$kawasaki = new KawasakiScraper();
$kawasaki->scrape();
$kawasaki->test();
