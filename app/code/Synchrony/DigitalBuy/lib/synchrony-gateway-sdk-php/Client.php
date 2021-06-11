<?php

namespace Synchrony\Gateway;

/**
 * Class Client
 * @package Synchrony\Gateway
 */
class Client
{
    /**
     * Config key constants
     */
    const CONFIG_KEY_AUTHENTICATION_API_ENDPOINT = 'authentication_api_endpoint';
    const CONFIG_KEY_STATUS_INQUIRY_API_ENDPOINT = 'status_inquiry_api_endpoint';
    const CONFIG_KEY_BUYSERVICE_API_ENDPOINT = 'buyservice_api_endpoint';
    const CONFIG_KEY_CAPTURE_API_ENDPOINT = 'capture_api_endpoint';
    const CONFIG_KEY_DIGITALBUY_API_MERCHANT_ID = 'digitalbuy_api_merchant_id';
    const CONFIG_KEY_DIGITALBUY_API_PASSWORD = 'digitalbuy_api_password';
    const CONFIG_KEY_BUYSERVICE_API_USERNAME = 'buyservice_api_username';
    const CONFIG_KEY_BUYSERVICE_API_PASSWORD = 'buyservice_api_password';
    const CONFIG_KEY_API_TIMEOUT = 'timeout';
    const CONFIG_KEY_SANDBOX = 'sandbox';
    const CONFIG_KEY_PROXY_ENABLED = 'proxy_enabled';
    const CONFIG_KEY_PROXY_HOST = 'proxy_host';
    const CONFIG_KEY_PROXY_PORT = 'proxy_port';

    /**
     * Basic parameters for API
     *
     * @var array
     */
    private $config = [
        self::CONFIG_KEY_AUTHENTICATION_API_ENDPOINT => null,
        self::CONFIG_KEY_STATUS_INQUIRY_API_ENDPOINT => null,
        self::CONFIG_KEY_BUYSERVICE_API_ENDPOINT => null,
        self::CONFIG_KEY_DIGITALBUY_API_MERCHANT_ID => '',
        self::CONFIG_KEY_DIGITALBUY_API_PASSWORD => '',
        self::CONFIG_KEY_BUYSERVICE_API_USERNAME => '',
        self::CONFIG_KEY_BUYSERVICE_API_PASSWORD => '',
        self::CONFIG_KEY_API_TIMEOUT => 5,
        self::CONFIG_KEY_SANDBOX => true,
        self::CONFIG_KEY_PROXY_ENABLED => false,
        self::CONFIG_KEY_PROXY_HOST => '',
        self::CONFIG_KEY_PROXY_PORT => ''
    ];

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Retrieve Authentication API endpoint from config
     *
     * @return string
     */
    private function getAuthenticationApiEndpoint()
    {
        return $this->config[self::CONFIG_KEY_AUTHENTICATION_API_ENDPOINT];
    }

    /**
     * Retrieve Status Inquiry API endpoint from config
     *
     * @return string
     */
    private function getStatusInquiryApiEndpoint()
    {
        return $this->config[self::CONFIG_KEY_STATUS_INQUIRY_API_ENDPOINT];
    }

    /**
     * Retrieve Buy Service API endpoint from config
     *
     * @return string
     */
    private function getBuyServiceApiEndpoint()
    {
        return $this->config[self::CONFIG_KEY_BUYSERVICE_API_ENDPOINT];
    }

    /**
     * Retrieve Capture API endpoint from config
     *
     * @return string
     */
    private function getCaptureApiEndpoint()
    {
        return $this->config[self::CONFIG_KEY_CAPTURE_API_ENDPOINT];
    }

    /**
     * Retrieve API request timeout from config
     *
     * @return int
     */
    private function getApiTimeout()
    {
        return (int)$this->config[self::CONFIG_KEY_API_TIMEOUT];
    }

    /**
     * Retrieve sandbox flag from config
     *
     * @return bool
     */
    private function getIsSandbox()
    {
        return (bool)$this->config[self::CONFIG_KEY_SANDBOX];
    }

    /**
     * Retrieve new User Token from Digital Buy API
     *
     * @return array
     */
    public function getDigitalBuyToken()
    {
        return $this->getDigitalBuyApiModel()->getToken();
    }

    /**
     * Get actual auth API endpoint
     *
     * @return string
     */
    public function getDigitalBuyTokenApiEndpoint()
    {
        return $this->getDigitalBuyApiModel()->getAuthEndpoint();
    }

    /**
     * Retrieve Digital Buy transaction status
     *
     * @param string $userToken
     * @return array
     */
    public function getDigitalBuyStatus($userToken)
    {
        return $this->getDigitalBuyApiModel()->getStatus($userToken);
    }

    /**
     * Get actual status inquiry API endpoint
     *
     * @return string
     */
    public function getDigitalBuyStatusApiEndpoint()
    {
        return $this->getDigitalBuyApiModel()->getStatusEndpoint();
    }

    /**
     * Capture offline/Authorize only transactions
     *
     * @param array $data
     * @return array
     */
    public function forcePurchase(array $data)
    {
        return $this->getBuyServiceApiModel()->forcePurchase($data);
    }

    /**
     * Retrieve Capture API endpoint from config
     *
     * @return string
     */
    public function getDigitalBuyCaptureApiEndpoint()
    {
        return $this->getCaptureApiModel()->getCaptureApiEndpoint();
    }

    /**
     * Process Capture
     *
     * @param array $data
     * @return array
     */
    public function capture(array $data)
    {
        return $this->getCaptureApiModel()->capture($data);
    }

    /**
     * Get force purchase API endpoint
     *
     * @return string
     */
    public function getForcePurchaseApiEndpoint()
    {
        return $this->getBuyServiceApiModel()->getApiEndpoint();
    }

    /**
     * Process Refund/Returns
     *
     * @param array $data
     * @return array
     */
    public function refund(array $data)
    {
        return $this->getBuyServiceApiModel()->refund($data);
    }

    /**
     * Get refund API endpoint
     *
     * @return string
     */
    public function getRefundApiEndpoint()
    {
        return $this->getBuyServiceApiModel()->getApiEndpoint();
    }

    /**
     * Retrieve Digital Buy API Merchant ID from config
     *
     * @return string
     */
    private function getDigitalBuyApiMerchantId()
    {
        return $this->config[self::CONFIG_KEY_DIGITALBUY_API_MERCHANT_ID];
    }

    /**
     * Retrieve Digital Buy API Password from config
     *
     * @return string
     */
    private function getDigitalBuyApiPassword()
    {
        return $this->config[self::CONFIG_KEY_DIGITALBUY_API_PASSWORD];
    }

    /**
     * Retrieve buy Service API Username from config
     *
     * @return string
     */
    private function getBuyServiceApiUsername()
    {
        return $this->config[self::CONFIG_KEY_BUYSERVICE_API_USERNAME];
    }

    /**
     * Retrieve buy Service API password from config
     *
     * @return string
     */
    private function getBuyServiceApiPassword()
    {
        return $this->config[self::CONFIG_KEY_BUYSERVICE_API_PASSWORD];
    }

    /**
     * Retrieve API Proxy info from config
     *
     * @return array|null
     */
    public function getProxy()
    {
        if ($this->config[self::CONFIG_KEY_PROXY_ENABLED]) {
            return [
                self::CONFIG_KEY_PROXY_HOST => $this->config[self::CONFIG_KEY_PROXY_HOST],
                self::CONFIG_KEY_PROXY_PORT => $this->config[self::CONFIG_KEY_PROXY_PORT],
            ];
        }
        return null;
    }


    /**
     * Create Digital Buy API model instance
     *
     * @return Client\DigitalBuy
     */
    private function getDigitalBuyApiModel()
    {
        return new Client\DigitalBuy(
            $this->getDigitalBuyApiMerchantId(),
            $this->getDigitalBuyApiPassword(),
            $this->getIsSandbox(),
            $this->getApiTimeout(),
            $this->getAuthenticationApiEndpoint(),
            $this->getStatusInquiryApiEndpoint(),
            $this->getProxy()
        );
    }

    /**
     * Create BuyService API model instance
     *
     * @return Client\BuyService
     */
    private function getBuyServiceApiModel()
    {
        return new Client\BuyService(
            $this->getBuyServiceApiUsername(),
            $this->getBuyServiceApiPassword(),
            $this->getIsSandbox(),
            $this->getApiTimeout(),
            $this->getBuyServiceApiEndpoint(),
            $this->getProxy()
        );
    }

    /**
     * Create Capture API model instance
     *
     * @return Client\DigitalBuy
     */
    private function getCaptureApiModel()
    {
        return new Client\DigitalBuy(
            $this->getDigitalBuyApiMerchantId(),
            $this->getDigitalBuyApiPassword(),
            $this->getIsSandbox(),
            $this->getApiTimeout(),
            $this->getCaptureApiEndpoint()
        );
    }
}
