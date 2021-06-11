<?php

namespace Synchrony\DigitalBuy\Model\Api;

use Synchrony\DigitalBuy\Gateway\Config\AbstractPaymentConfig as Config;
use Synchrony\Gateway\Client;

/**
 * Class ClientFactory
 * @package Synchrony\DigitalBuy\Model\Api
 */
class ClientFactory
{
    /**
     * @var Config
     */
    private $config;

    /**
     * ClientFactory constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Create Synchrony API Client instance
     *
     * @param string|int|null $storeId
     * @return \Synchrony\Gateway\Client
     */
    public function create($storeId = null)
    {
        $paymentConfigParams = [
            Client::CONFIG_KEY_AUTHENTICATION_API_ENDPOINT => $this->config->getAuthenticationApiEndpoint($storeId),
            Client::CONFIG_KEY_STATUS_INQUIRY_API_ENDPOINT => $this->config->getStatusInquiryApiEndpoint($storeId),
            Client::CONFIG_KEY_CAPTURE_API_ENDPOINT => $this->config->getCaptureApiEndpoint($storeId),
            Client::CONFIG_KEY_BUYSERVICE_API_ENDPOINT => $this->config->getBuyServiceApiEndpoint($storeId),
            Client::CONFIG_KEY_DIGITALBUY_API_MERCHANT_ID => $this->config->getDigitalBuyApiMerchantId($storeId),
            Client::CONFIG_KEY_DIGITALBUY_API_PASSWORD => $this->config->getDigitalBuyApiPassword($storeId),
            Client::CONFIG_KEY_BUYSERVICE_API_USERNAME => $this->config->getBuyServiceApiUsername($storeId),
            Client::CONFIG_KEY_BUYSERVICE_API_PASSWORD => $this->config->getBuyServiceApiPassword($storeId),
            Client::CONFIG_KEY_API_TIMEOUT => $this->config->getApiTimeout($storeId),
            Client::CONFIG_KEY_SANDBOX => $this->config->getIsSandboxMode($storeId),
            Client::CONFIG_KEY_PROXY_ENABLED => $this->config->getIsApiProxyEnabled($storeId),
            Client::CONFIG_KEY_PROXY_HOST => $this->config->getApiProxyHost($storeId),
            Client::CONFIG_KEY_PROXY_PORT => $this->config->getApiProxyPort($storeId)
        ];
        return new Client($paymentConfigParams);
    }
}
