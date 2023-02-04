<?php

namespace App\Scraper;

use Brick\Browser\By;

class KawasakiScraper extends AbstractScraper
{
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
            $type = $this->findSelect(By::id("NICK"));
            $type = $this->findSelect(By::id("YEAR"));
            $model = $this->findSelect(By::id("MODEL"));
            $vin = $this->findSelect(By::id("VIN_SEL"));
            $engine = $this->findSelect(By::id("ENGINE_SEL"));
            $country = $this->findSelect(By::id("COUNTRY"));
            // $this->ajax();
            // TODO: implement
            die();
            $this->back();
        }
    }
}
