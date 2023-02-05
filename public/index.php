<?php
require_once __DIR__ . "/../bootstrap.php";

use App\Scraper\KawasakiScraper;

(new KawasakiScraper())->scrape();
