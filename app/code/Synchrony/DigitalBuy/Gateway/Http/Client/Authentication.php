<?php

namespace Synchrony\DigitalBuy\Gateway\Http\Client;

class Authentication extends AbstractClient
{
    /**
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function process(array $data)
    {
        try {
            $response = $this->getApiClient()->getDigitalBuyToken();
        } catch (\Exception $e) {
            $this->processClientException($e, 'Unable to retrieve user token');
            throw $e;
        }
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getApiEndpoint()
    {
        return $this->getApiClient()->getDigitalBuyTokenApiEndpoint();
    }
}
