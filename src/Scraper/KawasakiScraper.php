<?php

namespace App\Scraper;

use App\Constant\ScraperConstant;
use App\Entity\KawasakiMotor;
use App\Repository\KawasakiMotorRepository;
use Brick\Browser\By;

class KawasakiScraper extends AbstractScraper
{
    private KawasakiMotorRepository $kawasakiMotorRepository;

    public function __construct()
    {
        $this->kawasakiMotorRepository = new KawasakiMotorRepository();
        parent::__construct();
    }

    public function scrape(): void
    {
        $this->login();
        $this->processCatalogPage();
    }

    protected function login(): void
    {
        $this->open($_ENV["KAWASAKI_BASE_URL"].$_ENV["KAWASAKI_LOGIN_PATH"]);
        $username = $this->findTextControl(By::id("LoginForm_username"));
        $username->setValue($_ENV["KAWASAKI_USERNAME"]);
        $password = $this->findTextControl(By::id("LoginForm_password"));
        $password->setValue($_ENV["KAWASAKI_PASSWORD"]);

        $this->submit(By::id("login-form"));

        $this->checkLoginSuccess($this->getUrl(), $_ENV["KAWASAKI_BASE_URL"].$_ENV["KAWASAKI_LOGIN_PATH"]);
    }

    private function processCatalogPage(): void
    {
        $this->click(By::cssSelector("a[href='".$_ENV["KAWASAKI_MAIN_CATALOG_PATH"]."']"));
        $this->click(By::cssSelector("a[href='".$_ENV["KAWASAKI_CATALOG_PATH"]."']"));

        $catalogs = $this->find(By::className("thumbnail"));
        foreach($catalogs->all() as $catalog) {
            $this->click(By::cssSelector("a[href='".$catalog->getAttribute("href")."']"));
            $types = $this->findSelect(By::id("NICK"))->getOptions();
            unset($types[0]); // We don't need the first one because the first one is the option "Válassz!".

            $id = 0;
            $url = $_ENV["KAWASAKI_BASE_URL"].$_ENV["KAWASAKI_CATALOG_LIST_PATH"];
            $method = ScraperConstant::SCRAPER_AJAX_POST_CODE;
            $responseType = ScraperConstant::SCRAPER_AJAX_RESPONSE_JSON;
            $vehicleType = explode("=", explode("?", $this->getUrl())[1])[1];
            foreach($types as $type) {
                $requestData = [
                    "data" => [
                        "NICK" => $type->getText(),
                        "YEAR" => "",
                        "MODEL" => "",
                        "VIN_SEL" => "",
                        "ENGINE_SEL" => "",
                        "COUNTRY" => "",
                        "BRAND" => $vehicleType
                    ]
                ];
                $response = $this->makeAjax($method, $url, $requestData, $responseType);

                $years = $response["data"]["YEAR"];
                foreach($years as $year) {
                    $requestData["data"]["YEAR"] = $year;
                    $response = $this->makeAjax($method, $url, $requestData, $responseType);

                    $models = $response["data"]["MODEL"];
                    foreach($models as $model) {
                        $requestData["data"]["MODEL"] = $model;
                        $response = $this->makeAjax($method, $url, $requestData, $responseType);

                        $vins = $response["data"]["VIN_SEL"];
                        foreach($vins as $vin) {
                            $requestData["data"]["VIN_SEL"] = $vin;
                            $response = $this->makeAjax($method, $url, $requestData, $responseType);

                            $engines = $response["data"]["ENGINE_SEL"];
                            foreach($engines as $engine) {
                                $requestData["data"]["ENGINE_SEL"] = $engine;
                                $response = $this->makeAjax($method, $url, $requestData, $responseType);

                                $countries = $response["data"]["COUNTRY"];
                                foreach($countries as $country) {
                                    $motor = new KawasakiMotor();
                                    $motor->setId(++$id);
                                    $motor->setType($type->getText());
                                    $motor->setYear($year);
                                    $motor->setModel($model);
                                    $motor->setVin($vin);
                                    $motor->setEngine($engine);
                                    $motor->setCountry($country);
                                    $motor->setColors($response["color"]);
                                    $motor->setCatalog($response["dir"]);
                                    $motor->setVehicleType($vehicleType);
                                    $this->kawasakiMotorRepository->save($motor);
                                }
                            }
                        }
                    }
                }
            }
            $this->back();
        }
    }
}
