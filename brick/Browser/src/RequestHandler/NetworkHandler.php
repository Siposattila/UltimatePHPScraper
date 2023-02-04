<?php

namespace Brick\Browser\RequestHandler;

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
            CURLOPT_COOKIE => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_USERAGENT => $request->getHeader("User-Agent"),
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => __DIR__ . "/cacert.pem"
        ];

        $ch = curl_init($request->getHost());
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $result = Response::parse($response);

        return $result;
    }
}
