<?php

namespace Synchrony\Gateway\Client;

class Exception extends \Exception
{
    /**
     * Request headers
     *
     * @var string
     */
    private $requestHeaders;

    /**
     * Request body
     *
     * @var string
     */
    private $requestBody;

    /**
     * Response headers
     *
     * @var string
     */
    private $responseHeaders;

    /**
     * Response body
     *
     * @var string
     */
    private $responseBody;

    /**
     * Set request headers string
     *
     * @param string $headers
     * @return $this
     */
    public function setRequestHeaders($headers)
    {
        $this->requestHeaders = $headers;
        return $this;
    }

    /**
     * Get request headers
     *
     * @return string
     */
    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    /**
     * Set request body
     *
     * @param $body
     * @return $this
     */
    public function setRequestBody($body)
    {
        $this->requestBody = $body;
        return $this;
    }

    /**
     * Get request body
     *
     * @return string
     */
    public function getRequestBody()
    {
        return $this->requestBody;
    }

    /**
     * Set response headers string
     *
     * @param string $headers
     * @return $this
     */
    public function setResponseHeaders($headers)
    {
        $this->responseHeaders = $headers;
        return $this;
    }

    /**
     * Get response headers
     *
     * @return string
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * Set response body
     *
     * @param $body
     * @return $this
     */
    public function setResponseBody($body)
    {
        $this->responseBody = $body;
        return $this;
    }

    /**
     * Get response body
     *
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }
}
