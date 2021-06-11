<?php

namespace Synchrony\DigitalBuy\Gateway\Http\Client;

class ForcePurchase extends AbstractClient
{
    /**
     * Process http request
     * @param array $data
     * @return mixed
     */
    protected function process(array $data)
    {
        try {
            $response = $this->getApiClient()->forcePurchase($data);
        } catch (\Exception $e) {
            $this->processClientException($e, 'Force purchase request failed');
            throw $e;
        }
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getApiEndpoint()
    {
        return $this->getApiClient()->getForcePurchaseApiEndpoint();
    }
}
