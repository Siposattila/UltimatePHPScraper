<?php

namespace App\Constant;

class ScraperConstant
{
    public const SCRAPER_AJAX_GET_CODE = 0;
    public const SCRAPER_AJAX_POST_CODE = 1;
    public const SCRAPER_AJAX_PUT_CODE = 2;
    public const SCRAPER_AJAX_DELETE_CODE = 3;

    public const SCRAPER_AJAX_METHODS = [
        self::SCRAPER_AJAX_GET_CODE => "GET",
        self::SCRAPER_AJAX_POST_CODE => "POST",
        self::SCRAPER_AJAX_PUT_CODE => "PUT",
        self::SCRAPER_AJAX_DELETE_CODE >="DELETE"
    ];

    public const SCRAPER_AJAX_RESPONSE_DOCUMENT = 0;
    public const SCRAPER_AJAX_RESPONSE_JSON = 1;
    public const SCRAPER_AJAX_RESPONSE_TEXT = 2;

    public const SCRAPER_AJAX_RESPONSES = [
        self::SCRAPER_AJAX_RESPONSE_DOCUMENT => true,
        self::SCRAPER_AJAX_RESPONSE_JSON => true,
        self::SCRAPER_AJAX_RESPONSE_TEXT => true
    ];
}
