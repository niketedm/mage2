<?php

namespace Synchrony\Gateway\Client;

/**
 * Class Http
 * @package Synchrony\Gateway\Client
 */
class Http extends AbstractTransport
{
    /**
     * Submit POST request
     *
     * @param array|string $postData
     * @param bool $sendAsJson
     * @return $this
     */
    public function post($postData, $sendAsJson = false)
    {
        $this->init();
        $headers = [];

        if ($sendAsJson) {
            $postData = is_array($postData) ? json_encode($postData) : $postData;
            $headers[] = 'Content-Type: application/json; charset=utf-8';
        } else {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $postData = !is_array($postData) ? $postData : http_build_query($postData);
        }

        if ($headers) {
            $this->setHeaders($headers);
        }
        $this->setPostData($postData);

        $response = $this->exec();

        if ($response === false) {
            throw new \Exception($this->lastError);
        }

        return $response;
    }
}
