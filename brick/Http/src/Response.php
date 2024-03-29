<?php

declare(strict_types=1);

namespace Brick\Http;

/**
 * @todo make final
 *
 * Represents an HTTP response to send back to the client.
 */
class Response extends Message
{
    /**
     * Mapping of Status Code to Reason Phrase.
     *
     * @var array
     */
    private static $statusCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported'
    ];

    /**
     * @var int
     */
    private $statusCode = 200;

    /**
     * @var string
     */
    private $reasonPhrase = 'OK';

    /**
     * @var \Brick\Http\Cookie[]
     */
    private $cookies = [];

    /**
     * Parses a raw response string, including headers and body, and returns a Response object.
     *
     * @param string $response
     *
     * @return \Brick\Http\Response
     *
     * @throws \RuntimeException
     */
    public static function parse(string $response) : Response
    {
        $responseObject = new Response();

        if (preg_match('/^HTTP\/([0-9]\.[0-9]) ([0-9]{3}) .*\r\n/', $response, $matches) !== 1) {
            throw new \RuntimeException('Could not parse response (error 1).');
        }

        [$line, $protocolVersion, $statusCode] = $matches;

        $responseObject->setProtocolVersion($protocolVersion);
        $responseObject->setStatusCode((int) $statusCode);

        $response = substr($response, strlen($line));

        for (;;) {
            $pos = strpos($response, Message::CRLF);
            if ($pos === false) {
                throw new \RuntimeException('Could not parse response (error 2).');
            }

            if ($pos === 0) {
                break;
            }

            $header = substr($response, 0, $pos);

            if (preg_match('/^(\S+):\s*(.*)$/', $header, $matches) !== 1) {
                throw new \RuntimeException('Could not parse response (error 3).');
            }

            [$line, $name, $value] = $matches;

            if (strtolower($name) === 'set-cookie') {
                $responseObject->setCookie(Cookie::parse($value));
            } else {
                $responseObject->addHeader($name, $value);
            }

            $response = substr($response, strlen($line) + 2);
        }

        $body = substr($response, 2);

        $responseObject->setContent($body);

        return $responseObject;
    }

    /**
     * Returns the status code of this response.
     *
     * @return int The status code.
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Returns the reason phrase of this response.
     *
     * @return string
     */
    public function getReasonPhrase() : string
    {
        return $this->reasonPhrase;
    }

    /**
     * @deprecated use withStatusCode()
     *
     * Sets the status code of this response.
     *
     * @param int         $statusCode   The status code.
     * @param string|null $reasonPhrase An optional reason phrase, or null to use the default.
     *
     * @return static
     *
     * @throws \InvalidArgumentException If the status code is not valid.
     */
    public function setStatusCode(int $statusCode, ?string $reasonPhrase = null) : Response
    {
        if ($statusCode < 100 || $statusCode > 999) {
            throw new \InvalidArgumentException('Invalid  status code: ' . $statusCode);
        }

        if ($reasonPhrase === null) {
            $reasonPhrase = isset(self::$statusCodes[$statusCode])
                ? self::$statusCodes[$statusCode]
                : 'Unknown';
        } else {
            $reasonPhrase = (string) $reasonPhrase;
        }

        $this->statusCode   = $statusCode;
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    public function withStatusCode(int $statusCode, ?string $reasonPhrase = null): Response
    {
        $that = clone $this;
        $that->setStatusCode($statusCode, $reasonPhrase);

        return $that;
    }

    /**
     * Returns the cookies currently set on this response.
     *
     * @return \Brick\Http\Cookie[]
     */
    public function getCookies() : array
    {
        return $this->cookies;
    }

    /**
     * @deprecated use withCookie()
     *
     * Sets a cookie on this response.
     *
     * @param \Brick\Http\Cookie $cookie The cookie to set.
     *
     * @return static This response.
     */
    public function setCookie(Cookie $cookie) : Response
    {
        $this->cookies[] = $cookie;
        $this->addHeader('Set-Cookie', (string) $cookie);

        return $this;
    }

    public function withCookie(Cookie $cookie): Response
    {
        $that = clone $this;
        $that->setCookie($cookie);

        return $that;
    }

    /**
     * @deprecated use withoutCookies()
     *
     * Removes all cookies from this response.
     *
     * @return static This response.
     */
    public function removeCookies() : Response
    {
        $this->cookies = [];

        return $this->removeHeader('Set-Cookie');
    }

    public function withoutCookies(): Response
    {
        $that = clone $this;
        $that->cookies = [];

        return $that->withoutHeader('Set-Cookie');
    }

    /**
     * @deprecated use withContent()
     *
     * @param string|resource $content
     *
     * @return static This response.
     */
    public function setContent($content) : Response
    {
        if (is_resource($content)) {
            $body = new MessageBodyResource($content);
        } else {
            $body = new MessageBodyString($content);
        }

        return $this->setBody($body);
    }

    /**
     * @param string|resource $content
     */
    public function withContent($content): Response
    {
        $that = clone $this;
        $that->setContent($content);

        return $that;
    }

    /**
     * Returns whether this response has an informational status code, 1xx.
     *
     * @return bool
     */
    public function isInformational() : bool
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * Returns whether this response has a successful status code, 2xx.
     *
     * @return bool
     */
    public function isSuccessful() : bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Returns whether this response has a redirection status code, 3xx.
     *
     * @return bool
     */
    public function isRedirection() : bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Returns whether this response has a client error status code, 4xx.
     *
     * @return bool
     */
    public function isClientError() : bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Returns whether this response has a server error status code, 5xx.
     *
     * @return bool
     */
    public function isServerError() : bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Returns whether this response has the given status code.
     *
     * @param int $statusCode
     *
     * @return bool
     */
    public function isStatusCode(int $statusCode) : bool
    {
        return $this->statusCode === $statusCode;
    }

    /**
     * Sends the response.
     *
     * This method will fail (return `false`) if the headers have been already sent.
     *
     * @return bool Whether the response has been successfully sent.
     */
    public function send() : bool
    {
        if (headers_sent()) {
            return false;
        }

        header($this->getStartLine());

        foreach ($this->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header($name . ': ' . $value, false);
            }
        }

        echo (string) $this->body;

        flush();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartLine() : string
    {
        return sprintf('HTTP/%s %d %s', $this->protocolVersion, $this->statusCode, $this->reasonPhrase);
    }
}
