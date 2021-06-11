<?php

namespace Synchrony\Gateway\Client;

abstract class AbstractTransport
{
    /**
     * Request URL
     *
     * @var string
     */
    protected $url;

    /**
     * Http Auth Username
     *
     * @var string
     */
    protected $authUsername;

    /**
     * Http Auth Password
     *
     * @var string
     */
    protected $authPassword;

    /**
     * Request body
     *
     * @var string
     */
    protected $postData;

    /**
     * Request timeout
     *
     * @var int
     */
    protected $timeout;

    /**
     * CURL resource
     *
     * @var resource
     */
    protected $curlResource;

    /**
     * Request headers
     *
     * @var string
     */
    protected $lastRequestHeaders;

    /**
     * Request body
     *
     * @var string
     */
    protected $lastRequestBody;

    /**
     * Response code
     *
     * @var string
     */
    protected $lastResponseCode;

    /**
     * Response headers
     *
     * @var string
     */
    protected $lastResponseHeaders;

    /**
     * Response body
     *
     * @var string
     */
    protected $lastResponseBody;

    /**
     * Request error
     *
     * @var string
     */
    protected $lastError;

    /**
     * Request error
     * Description:
     * - proxy_host: key which holds API proxy host url
     * - proxy_port: key which holds API proxy port
     *
     * @var array
     */
    protected $proxy = [
        'proxy_host'=>'',
        'proxy_port'=>''
    ];

    /**
     * Http constructor.
     * @param string $url
     * @param int $timeout
     * @param array $proxy
     *
     */
    public function __construct($url, $timeout = 5, $proxy = null)
    {
        $this->url = $url;
        $this->timeout = (int) $timeout;
        if (is_array($proxy) && !empty($proxy)) {
            $this->proxy = array_merge($this->proxy, $proxy);
        }
    }

    /**
     * Get request headers
     *
     * @return string|null
     */
    public function getLastRequestHeaders()
    {
        return $this->lastRequestHeaders;
    }

    /**
     * Get request body
     *
     * @return string|null
     */
    public function getLastRequestBody()
    {
        return $this->lastRequestBody;
    }

    /**
     * Get response headers
     *
     * @return string|null
     */
    public function getLastResponseHeaders()
    {
        return $this->lastResponseHeaders;
    }

    /**
     * Get response body
     *
     * @return string|null
     */
    public function getLastResponseBody()
    {
        return $this->lastResponseBody;
    }

    /**
     * Get response body
     *
     * @return string|null
     */
    public function getLastResponseCode()
    {
        return $this->lastResponseCode;
    }

    /**
     * Init curl resource
     *
     * @return $this
     */
    protected function init()
    {
        $this->lastRequestHeaders = null;
        $this->lastRequestBody = null;
        $this->lastResponseHeaders = null;
        $this->lastResponseBody = null;
        $this->lastError = null;
        $this->curlResource = curl_init($this->url);
        curl_setopt($this->curlResource, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->curlResource, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->curlResource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlResource, CURLINFO_HEADER_OUT, true);
        if ($this->authUsername) {
            curl_setopt($this->curlResource, CURLOPT_USERPWD, $this->authUsername . ":" . $this->authPassword);
        }
        $this->setProxy($this->proxy);
        $this->initHeaderFunction($this->lastResponseHeaders);
        return $this;
    }

    /**
     * Init response header callback
     *
     * @param $headers
     * @return $this
     */
    private function initHeaderFunction(&$headers)
    {
        curl_setopt(
            $this->curlResource,
            CURLOPT_HEADERFUNCTION,
            function ($resource, $header) use (&$headers) {
                $headers .= $header . "\n";
                $size = strlen($header);
                return $size;
            }
        );
        return $this;
    }

    /**
     * Set request headers
     *
     * @param array $headers
     * @return $this
     */
    protected function setHeaders(array $headers)
    {
        curl_setopt($this->curlResource, CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    /**
     * Set request proxy configuration
     *
     * @param array $proxy
     * @return $this
     */
    protected function setProxy(array $proxy)
    {
        if ($proxy['proxy_host']) {
            curl_setopt($this->curlResource, CURLOPT_PROXY, $proxy['proxy_host']);
        }
        if ($proxy['proxy_port']) {
            curl_setopt($this->curlResource, CURLOPT_PROXYPORT, $proxy['proxy_port']);
        }
        return $this;
    }

    /**
     * Set Auth Credentials
     *
     * @param string $username
     * @param string $password
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->authUsername = $username;
        $this->authPassword = $password;
    }

    /**
     * Set POST body
     *
     * @param $data
     * @return $this
     */
    protected function setPostData($data)
    {
        $this->lastRequestBody = $data;
        curl_setopt($this->curlResource, CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    /**
     * Execute request
     *
     * @return string|bool
     */
    protected function exec()
    {
        $response = curl_exec($this->curlResource);

        $this->lastRequestHeaders = curl_getinfo($this->curlResource, CURLINFO_HEADER_OUT);
        $this->lastResponseCode = curl_getinfo($this->curlResource, CURLINFO_HTTP_CODE);
        $this->lastResponseBody = $response;
        $this->lastError = curl_error($this->curlResource);
        curl_close($this->curlResource);

        return $response;
    }

    /**
     * Submit POST request
     *
     * @param $postData
     * @return mixed
     */
    abstract public function post($postData);
}
