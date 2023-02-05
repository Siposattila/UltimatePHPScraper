<?php

namespace Brick\Browser\RequestHandler;

use App\Logger\Logger;
use Brick\Http\Request;
use Brick\Http\RequestHandler;
use Brick\Http\Response;

class NetworkHandler implements RequestHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(Request $request) : Response
    {
        $request->withHeader("Connection", "close");

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_COOKIE => $request->withCookies($request->getCookie())->getHeader("Cookie"),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_ENCODING => "",
            CURLOPT_USERAGENT => $request->getHeader("User-Agent"),
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => __DIR__ . "/cacert.pem"
        ];

        if ($request->isMethod("POST")) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($request->getPost());
            Logger::debug(urldecode(http_build_query($request->getPost())), false);
        }

        Logger::info($request->getUrl());
        $ch = curl_init($request->getUrl());
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        $result = Response::parse($response);

        return $result;
    }
}
