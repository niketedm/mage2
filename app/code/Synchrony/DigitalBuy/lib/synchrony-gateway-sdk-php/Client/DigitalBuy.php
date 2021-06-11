<?php

namespace Synchrony\Gateway\Client;

/**
 * Class DigitalBuy
 * @package Synchrony\Gateway\Client
 */
class DigitalBuy
{
    /**
     * Test/Sandbox Endpoints
     */
    const TEST_AUTH_ENDPOINT_URL = 'https://ubuy.syf.com/DigitalBuy/authentication.do';
    const TEST_CAPTURE_ENDPOINT_URL = 'https://usvcs.syf.com/v1.0/capture';
    const TEST_STATUS_ENDPOINT_URL = 'https://usvcs.syf.com/v1.0/status/inquiry';

    /**
     * PROD Endpoints
     */
    const PROD_AUTH_ENDPOINT_URL = 'https://buy.syf.com/DigitalBuy/authentication.do';
    const PROD_CAPTURE_ENDPOINT_URL = 'https://svcs.syf.com/v1.0/capture';
    const PROD_STATUS_ENDPOINT_URL = 'https://svcs.syf.com/v1.0/status/inquiry';

    /**
     * HTTP Response Codes
     */
    const HTTP_RESPONSE_CODE_SUCCESS = 200;

    /**
     * Merchant ID
     *
     * @var string
     */
    private $merchantId;

    /**
     * Password
     *
     * @var string
     */
    private $password;

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
     * DigitalBuy Authentication API Endpoint
     *
     * @var string|null
     */
    private $overrideAuthEndpoint;

    /**
     * DigitalBuy Capture API Endpoint
     *
     * @var string|null
     */
    private $overrideCaptureEndpoint;

    /**
     * DigitalBuy Status Inquiry API Endpoint
     *
     * @var string|null
     */
    private $overrideStatusInquiryEndpoint;

    /**
     * Proxy Info
     * @var array
     */
    private $proxy;

    /**
     * DigitalBuy constructor.
     * @param string $merchantId
     * @param string $password
     * @param bool $isSandbox
     * @param int $timeout in seconds
     * @param string|null $overrideAuthEndpoint
     * @param string|null $overrideCaptureEndpoint
     * @param string|null $overrideStatusInquiryEndpoint
     * @param array|null $proxy
     */
    public function __construct(
        $merchantId,
        $password,
        $isSandbox = true,
        $timeout = 5,
        $overrideAuthEndpoint = null,
        $overrideCaptureEndpoint = null,
        $overrideStatusInquiryEndpoint = null,
        $proxy = null
    ) {
        $this->merchantId = $merchantId;
        $this->password = $password;
        $this->isSandbox = (bool) $isSandbox;
        $this->timeout = (int) $timeout;
        $this->overrideAuthEndpoint = $overrideAuthEndpoint;
        $this->overrideCaptureEndpoint = $overrideCaptureEndpoint;
        $this->overrideStatusInquiryEndpoint = $overrideStatusInquiryEndpoint;
        $this->proxy = $proxy;
    }

    /**
     * Call Authentication API
     *
     * @return array
     * @throws Exception
     */
    public function getToken()
    {
        $postData = [
            'merchantId' => $this->merchantId,
            'password' => $this->password
        ];

        try {
            $transport = new Http($this->getAuthEndpoint(), $this->timeout, $this->proxy);
            $response = $transport->post($postData);
            $result = json_decode($response, true);
            if ($result === null) {
                throw new \Exception('Unable to decode Authentication API response: ' . json_last_error_msg());
            }
        } catch (\Exception $e) {
            $clientException = new Exception('Error while calling Authentication API: ' . $e->getMessage());
            if (!empty($transport)) {
                $clientException->setRequestHeaders($transport->getLastRequestHeaders())
                    ->setRequestBody($transport->getLastRequestBody())
                    ->setResponseHeaders($transport->getLastResponseHeaders())
                    ->setResponseBody($transport->getLastResponseBody());
            }
            throw $clientException;
        }

        return $result;
    }

    /**
     * Call Capture API
     *
     * @param array $postData
     * @return array
     * @throws Exception
     */
    public function capture(array $postData)
    {
        try {
            $transport = new Http($this->getCaptureApiEndpoint(), $this->timeout);
            $transport->setBasicAuthentication($this->merchantId, $this->password);
            $response = $transport->post($postData, true);
            $result = json_decode($response, true);
            if ($result === null) {
                throw new \Exception(
                    'Unable to decode Capture API response: ' . json_last_error_msg()
                );
            }
            if ($transport->getLastResponseCode() !== self::HTTP_RESPONSE_CODE_SUCCESS) {
                throw new \Exception(
                    'Unsuccessful Capture API response code: ' . $transport->getLastResponseCode()
                );
            }
        } catch (\Exception $e) {
            $clientException = new Exception('Error while calling Capture API: ' . $e->getMessage());
            if (!empty($transport)) {
                $clientException->setRequestHeaders($transport->getLastRequestHeaders())
                    ->setRequestBody($transport->getLastRequestBody())
                    ->setResponseHeaders($transport->getLastResponseHeaders())
                    ->setResponseBody($transport->getLastResponseBody());
            }
            throw $clientException;
        }

        return $result;
    }

    /**
     * Call Status Inquiry API
     *
     * @param string $userToken
     * @return array
     * @throws Exception
     */
    public function getStatus($userToken)
    {
        $postData = [
            'merchantNumber' => $this->merchantId,
            'password' => $this->password,
            'userToken' => $userToken
        ];

        try {
            $transport = new Http($this->getStatusEndpoint(), $this->timeout, $this->proxy);
            $response = $transport->post($postData, true);
            $result = json_decode($response, true);
            if ($result === null) {
                throw new \Exception('Unable to decode Status Inquiry API response: ' . json_last_error_msg());
            }
        } catch (\Exception $e) {
            $clientException = new Exception('Error while calling Status Inquiry API: ' . $e->getMessage());
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
     * Retrieve Authentication API endpoint
     *
     * @return string
     */
    public function getAuthEndpoint()
    {
        if (!empty($this->overrideAuthEndpoint)) {
            return $this->overrideAuthEndpoint;
        }

        return $this->isSandbox ? self::TEST_AUTH_ENDPOINT_URL : self::PROD_AUTH_ENDPOINT_URL;
    }

    /**
     * Retrieve Payment Capture API endpoint
     *
     * @return string
     */
    public function getCaptureApiEndpoint()
    {
        if (!empty($this->overrideCaptureEndpoint)) {
            return $this->overrideCaptureEndpoint;
        }

        return $this->isSandbox ? self::TEST_CAPTURE_ENDPOINT_URL : self::PROD_CAPTURE_ENDPOINT_URL;
    }

    /**
     * Retrieve Status Inquiry API endpoint
     *
     * @return string
     */
    public function getStatusEndpoint()
    {
        if (!empty($this->overrideStatusInquiryEndpoint)) {
            return $this->overrideStatusInquiryEndpoint;
        }

        return $this->isSandbox ? self::TEST_STATUS_ENDPOINT_URL : self::PROD_STATUS_ENDPOINT_URL;
    }
}
