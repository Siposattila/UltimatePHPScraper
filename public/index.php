<?php

use App\Scraper\TestScraper;

require_once __DIR__ . "/../bootstrap.php";

(new TestScraper)->scrape();
