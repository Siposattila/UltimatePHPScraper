<?php

namespace App\Scraper;

use App\Constant\ScraperConstant;
use App\Exception\ScraperAjaxFailedToGetResponse;
use App\Exception\ScraperFailedToLoginException;
use Brick\Browser\Browser;
use Brick\Browser\Client\Client;
use Brick\Browser\RequestHandler\NetworkHandler;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractScraper extends Browser
{
    private Client $client;
    protected Serializer $serializer;

    public function __construct()
    {
        $this->client = new Client(new NetworkHandler());
        $this->serializer = new Serializer([new ObjectNormalizer()], [
            new JsonEncoder(),
            new XmlEncoder(),
            new CsvEncoder(),
            new YamlEncoder()
        ]);
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

    protected function makeAjax(int $method, string $url, string|array|object $data = null, int $reponseType, array $headers = []): string|array
    {
        $response = $this->ajax(ScraperConstant::SCRAPER_AJAX_METHODS[$method], $url, $data, $headers);

        if ($reponseType == ScraperConstant::SCRAPER_AJAX_RESPONSE_JSON) {
            return $response->parseJson();
        }

        if ($reponseType == ScraperConstant::SCRAPER_AJAX_RESPONSE_TEXT) {
            return $response->getText();
        }

        if ($reponseType == ScraperConstant::SCRAPER_AJAX_RESPONSE_DOCUMENT) {
            return $response->getText();
        }

        throw new ScraperAjaxFailedToGetResponse("Failed to get response in the given reponse type!");
    }
}
