<?php

namespace Synchrony\DigitalBuy\Gateway\Http\Client;

class Adjust extends AbstractClient
{
    /**
     * Process http request
     * @param array $data
     * @return array
     */
    protected function process(array $data)
    {
        try {
            $response = $this->getApiClient()->refund($data);
        } catch (\Exception $e) {
            $this->processClientException($e, 'Adjust Request failed');
            throw $e;
        }
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getApiEndpoint()
    {
        return $this->getApiClient()->getRefundApiEndpoint();
    }
}
