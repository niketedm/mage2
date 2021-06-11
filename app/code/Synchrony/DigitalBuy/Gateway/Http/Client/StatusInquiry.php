<?php

namespace Synchrony\DigitalBuy\Gateway\Http\Client;

use Synchrony\DigitalBuy\Gateway\Request\DigitalBuyTokenDataBuilder;

class StatusInquiry extends AbstractClient
{
    /**
     * @param array $data
     * @return mixed
     */
    protected function process(array $data)
    {
        try {
            $response = $this->getApiClient()->getDigitalBuyStatus($data[DigitalBuyTokenDataBuilder::TOKEN_ID]);
        } catch (\Exception $e) {
            $this->processClientException($e, 'Status inquiry failed');
            throw $e;
        }
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getApiEndpoint()
    {
        return $this->getApiClient()->getDigitalBuyStatusApiEndpoint();
    }
}
