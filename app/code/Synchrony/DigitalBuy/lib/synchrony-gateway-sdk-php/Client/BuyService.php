<?php

namespace Synchrony\Gateway\Client;

class BuyService
{
    const TEST_ENDPOINT_URL = 'https://iwww.b2bcreditservices.com/BuyService';
    const PROD_ENDPOINT_URL = 'https://www.b2bcreditservices.com/BuyService';

    const FORCE_PURCHASE_REQ_NAME = 'forcePurchaseRequest';
    const FORCE_PURCHACE_REQ_PARM_NAME = 'forcePurchaseParm';
    const FORCE_PURCHASE_RESP_NAME = 'forcePurchaseResponse';
    const FORCE_PURCHASE_RESP_PARM_NAME = 'forcePurchaseResParm';
    const REFUND_REQ_NAME = 'adjustRequest';
    const REFUND_REQ_PARM_NAME = 'adjustmentParm';
    const REFUND_RESP_NAME = 'adjustResponse';
    const REFUND_RESP_PARM_NAME = 'adjustResParm';

    /**
     * Buyservice API Username
     *
     * @var string
     */
    private $apiUsername;

    /**
     * Buyservice API Password
     *
     * @var string
     */
    private $apiPassword;

    /**
     * Sandbox flag
     *
     * @var bool
     */
    private $isSandbox;

    /**
     * Request timeout
     *
     * @var int
     */
    private $timeout;

    /**
     * Buy Service API Endpoint
     *
     * @var string|null
     */
    private $overrideApiEndpoint;

    /**
     * Proxy Info
     * @var array|null
     */
    private $proxy;

    /**
     * DigitalBuy constructor.
     * @param string $apiUsername
     * @param string $apiPassword
     * @param bool $isSandbox
     * @param int $timeout in seconds
     * @param string|null $overrideApiEndpoint
     * @param array|null $proxy
     */
    public function __construct(
        $apiUsername,
        $apiPassword,
        $isSandbox = true,
        $timeout = 5,
        $overrideApiEndpoint = null,
        $proxy = null
    ) {
        $this->apiUsername = $apiUsername;
        $this->apiPassword = $apiPassword;
        $this->isSandbox = (bool) $isSandbox;
        $this->timeout = (int) $timeout;
        $this->overrideApiEndpoint = $overrideApiEndpoint;
        $this->proxy = $proxy;
    }

    /**
     * Capture Authorized transactions
     *
     * @param array $data
     * @return string
     */
    public function forcePurchase(array $data)
    {
        $xmlObject = new BuyService\RequestXml($this->apiUsername, $this->apiPassword);
        $postData = $xmlObject->getSoapRequestXml(
            $data,
            self::FORCE_PURCHASE_REQ_NAME,
            self::FORCE_PURCHACE_REQ_PARM_NAME
        );
        $responseProcessor = new BuyService\ResponseProcessor();

        try {
            $transport = new Soap($this->getApiEndpoint($this->isSandbox), $this->timeout, $this->proxy);
            $response = $transport->post($postData);
            $result = $responseProcessor->process(
                $response,
                self::FORCE_PURCHASE_RESP_NAME,
                self::FORCE_PURCHASE_RESP_PARM_NAME
            );
        } catch (\Exception $e) {
            $clientException = new Exception('Error while calling Force purchase API: ' . $e->getMessage());
            if (!empty($transport)) {
                $clientException->setRequestHeaders($transport->getLastRequestHeaders())
                    ->setRequestBody($transport->getLastRequestBody())
                    ->setResponseHeaders($transport->getLastResponseHeaders())
                    ->setResponseBody($transport->getlastResponseBody());
            }
            throw $clientException;
        }
        return $result;
    }

    /**
     * Process Refund/Returns
     *
     * @param array $data
     * @return string
     */
    public function refund(array $data)
    {
        $xmlObject = new BuyService\RequestXml($this->apiUsername, $this->apiPassword);
        $postData = $xmlObject->getSoapRequestXml(
            $data,
            self::REFUND_REQ_NAME,
            self::REFUND_REQ_PARM_NAME
        );
        $responseProcessor = new BuyService\ResponseProcessor();

        try {
            $transport = new Soap($this->getApiEndpoint($this->isSandbox), $this->timeout, $this->proxy);
            $response = $transport->post($postData);
            $result = $responseProcessor->process(
                $response,
                self::REFUND_RESP_NAME,
                self::REFUND_RESP_PARM_NAME
            );
        } catch (\Exception $e) {
            $clientException = new Exception('Error while calling Refund API: ' . $e->getMessage());
            if (!empty($transport)) {
                $clientException->setRequestHeaders($transport->getLastRequestHeaders())
                    ->setRequestBody($transport->getLastRequestBody())
                    ->setResponseHeaders($transport->getLastResponseHeaders())
                    ->setResponseBody($transport->getlastResponseBody());
            }
            throw $clientException;
        }

        return $result;
    }

    /**
     * Retrieve API endpoint
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        if (!empty($this->overrideApiEndpoint)) {
            return $this->overrideApiEndpoint;
        }

        return $this->isSandbox ? self::TEST_ENDPOINT_URL : self::PROD_ENDPOINT_URL;
    }
}
