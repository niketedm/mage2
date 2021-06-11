<?php

namespace Synchrony\Gateway\Client;

/**
 * Class Http
 * @package Synchrony\Gateway\Client
 */
class Soap extends AbstractTransport
{
    /**
     * @var array
     */
    private $allowedResponseCodes = [
        200,
        500,
    ];

    /**
     * Submit POST(XML payload) request
     *
     * @param string $postData
     * @return null|string
     */
    public function post($postData)
    {
        $this->init();
        $headers = [];

        $headers[] = 'Content-type: text/xml; charset=utf-8';
        $headers[] = 'Accept: text/xml';
        $headers[] = 'SOAPAction: ' . $this->url;

        if ($headers) {
            $this->setHeaders($headers);
        }
        $this->setPostData($postData);

        $response = $this->exec();

        if ($response === false) {
            throw new \Exception($this->lastError);
        } elseif (!in_array((int)$this->lastResponseCode, $this->allowedResponseCodes)) {
            $error = 'Unexpected HTTP Status Code: ' . $this->lastResponseCode;
            throw new \Exception($error);
        }

        return $response;
    }
}
