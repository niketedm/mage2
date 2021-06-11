<?php

namespace Synchrony\DigitalBuy\Gateway\Http\Client\Installment;

class Capture extends \Synchrony\DigitalBuy\Gateway\Http\Client\AbstractClient
{
    /**
     * Process Installment capture request
     *
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    protected function process(array $data)
    {
        try {
            $response = $this->getApiClient()->capture($data);
        } catch (\Exception $e) {
            $this->processClientException($e, 'Installment Capture request failed');
            throw $e;
        }
        return $response;
    }

    /**
     * Return Api Endpoint
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->getApiClient()->getDigitalBuyCaptureApiEndpoint();
    }
}
