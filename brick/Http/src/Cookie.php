<?php

declare(strict_types=1);

namespace Brick\Http;

/**
 * An HTTP cookie.
 *
 * @todo Max-Age support.
 */
class Cookie
{
    /**
     * The name of the cookie.
     *
     * @var string
     */
    private $name;

    /**
     * The value of the cookie.
     *
     * @var string
     */
    private $value;

    /**
     * The unix timestamp at which the cookie expires.
     *
     * Zero if the cookie should expire at the end of the browser session.
     *
     * @var int
     */
    private $expires = 0;

    /**
     * The path on which the cookie is valid, or null if not set.
     *
     * @var string|null
     */
    private $path;

    /**
     * The domain on which the cookie is valid, or null if not set.
     *
     * @var string|null
     */
    private $domain;

    /**
     * Whether the cookie should only be sent on a secure connection.
     *
     * @var bool
     */
    private $secure = false;

    /**
     * Whether the cookie should only be sent over the HTTP protocol.
     *
     * @var bool
     */
    private $httpOnly = false;

    /**
     * Class constructor.
     *
     * @param string $name  The name of the cookie.
     * @param string $value The value of the cookie.
     */
    public function __construct(string $name, string $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * Creates a cookie from the contents of a Set-Cookie header.
     *
     * @param string $string
     *
     * @return Cookie The cookie.
     *
     * @throws \InvalidArgumentException If the cookie string is not valid.
     */
    public static function parse(string $string) : Cookie
    {
        $parts = preg_split('/;\s*/', $string);
        $nameValue = explode('=', array_shift($parts), 2);

        if (count($nameValue) !== 2) {
            throw new \InvalidArgumentException('The cookie string is not valid.');
        }

        [$name, $value] = $nameValue;

        if ($name === '') {
            throw new \InvalidArgumentException('The cookie string is not valid.');
        }

        if ($value === '') {
            throw new \InvalidArgumentException('The cookie string is not valid.');
        }

        $value = rawurldecode($value);
        $expires = 0;
        $path = null;
        $domain = null;
        $secure = false;
        $httpOnly = false;

        foreach ($parts as $part) {
            switch (strtolower($part)) {
                case 'secure':
                    $secure = true;
                    break;

                case 'httponly':
                    $httpOnly = true;
                    break;

                default:
                    $elements = explode('=', $part, 2);
                    if (count($elements) === 2) {
                        switch (strtolower($elements[0])) {
                            case 'expires':
                                // Using @ to suppress the timezone warning, might not be the best thing to do.
                                if (is_int($time = @ strtotime($elements[1]))) {
                                    $expires = $time;
                                }
                                break;

                            case 'path':
                                $path = $elements[1];
                                break;

                            case 'domain':
                                $domain = strtolower(ltrim($elements[1], '.'));
                        }
                    }
            }
        }

        return (new Cookie($name, $value))
            ->withExpires($expires)
            ->withPath($path)
            ->withDomain($domain)
            ->withSecure($secure)
            ->withHttpOnly($httpOnly);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getExpires() : int
    {
        return $this->expires;
    }

    /**
     * @deprecated use withExpires()
     *
     * Sets the cookie expiry time.
     *
     * @param int $expires The unix timestamp at which the cookie expires, zero for a transient cookie.
     *
     * @return static This cookie.
     */
    public function setExpires(int $expires) : Cookie
    {
        $this->expires = $expires;

        return $this;
    }

    public function withExpires(int $expires): Cookie
    {
        $that = clone $this;
        $that->expires = $expires;

        return $that;
    }

    /**
     * @return string|null
     */
    public function getPath() : ?string
    {
        return $this->path;
    }

    /**
     * @deprecated use withPath()
     *
     * @param string|null $path
     *
     * @return static This cookie.
     */
    public function setPath(?string $path) : Cookie
    {
        $this->path = $path;

        return $this;
    }

    public function withPath(?string $path): Cookie
    {
        $that = clone $this;
        $that->path = $path;

        return $that;
    }

    /**
     * @return string|null
     */
    public function getDomain() : ?string
    {
        return $this->domain;
    }

    /**
     * @deprecated use withDomain()
     *
     * @param string|null $domain
     *
     * @return static This cookie.
     */
    public function setDomain(?string $domain) : Cookie
    {
        $this->domain = $domain;

        return $this;
    }

    public function withDomain(?string $domain): Cookie
    {
        $that = clone $this;
        $that->domain = $domain;

        return $that;
    }

    /**
     * @return bool
     */
    public function isHostOnly() : bool
    {
        return $this->domain === null;
    }

    /**
     * @return bool
     */
    public function isSecure() : bool
    {
        return $this->secure;
    }

    /**
     * @deprecated use withSecure()
     *
     * Sets whether this cookie should only be sent over a secure connection.
     *
     * @param bool $secure True to only send over a secure connection, false otherwise.
     *
     * @return static This cookie.
     */
    public function setSecure(bool $secure) : Cookie
    {
        $this->secure = $secure;

        return $this;
    }

    public function withSecure(bool $secure): Cookie
    {
        $that = clone $this;
        $that->secure = $secure;

        return $that;
    }

    /**
     * Returns whether to limit the scope of this cookie to HTTP requests.
     *
     * @return bool True if this cookie should only be sent over a secure connection, false otherwise.
     */
    public function isHttpOnly() : bool
    {
        return $this->httpOnly;
    }

    /**
     * @deprecated use withHttpOnly()
     *
     * Sets whether to limit the scope of this cookie to HTTP requests.
     *
     * Set to true to instruct the user agent to omit the cookie when providing access to
     * cookies via "non-HTTP" APIs (such as a web browser API that exposes cookies to scripts).
     *
     * This helps mitigate the risk of client side script accessing the protected cookie
     * (provided that the user agent supports it).
     *
     * @param bool $httpOnly
     *
     * @return static This cookie.
     */
    public function setHttpOnly(bool $httpOnly) : Cookie
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    public function withHttpOnly(bool $httpOnly): Cookie
    {
        $that = clone $this;
        $that->httpOnly = $httpOnly;

        return $that;
    }

    /**
     * Returns whether this cookie has expired.
     *
     * @return bool
     */
    public function isExpired() : bool
    {
        return $this->expires !== 0 && $this->expires < time();
    }

    /**
     * Returns whether the cookie is persistent.
     *
     * If false, the cookie should be discarded at the end of the session.
     * If true, the cookie should be discarded when the expiry time is reached.
     *
     * @return bool
     */
    public function isPersistent() : bool
    {
        return $this->expires !== 0;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        $cookie = $this->name . '=' . rawurlencode($this->value);

        if ($this->expires !== 0) {
            $cookie .= '; Expires=' . gmdate('r', $this->expires);
        }

        if ($this->domain !== null) {
            $cookie .= '; Domain=' . $this->domain;
        }

        if ($this->path !== null) {
            $cookie .= '; Path=' . $this->path;
        }

        if ($this->secure) {
            $cookie .= '; Secure';
        }

        if ($this->httpOnly) {
            $cookie .= '; HttpOnly';
        }

        return $cookie;
    }
}
