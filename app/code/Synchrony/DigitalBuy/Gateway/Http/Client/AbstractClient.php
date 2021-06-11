<?php

namespace Synchrony\DigitalBuy\Gateway\Http\Client;

use Synchrony\DigitalBuy\Model\Api\ClientFactory;
use Synchrony\DigitalBuy\Gateway\Request\StoreIdDataBuilder;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractClient
 */
abstract class AbstractClient implements ClientInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Logger
     */
    protected $paymentDataLogger;

    /**
     * @var ClientFactory
     */
    protected $apiClientFactory;

    /**
     * @var array
     */
    protected $clientConfig;

    /**
     * @var \Synchrony\Gateway\Client
     */
    private $apiClient;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Logger $paymentDataLogger
     * @param ClientFactory $apiClientFactory
     */
    public function __construct(LoggerInterface $logger, Logger $paymentDataLogger, ClientFactory $apiClientFactory)
    {
        $this->logger = $logger;
        $this->paymentDataLogger = $paymentDataLogger;
        $this->apiClientFactory = $apiClientFactory;
    }

    /**
     * @inheritdoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $this->clientConfig = $transferObject->getClientConfig();

        $data = $transferObject->getBody();
        $log = [
            'request' => $data,
            'client' => static::class
        ];
        $response = [];

        try {
            $response = $this->process($data);
        } catch (\Exception $e) {
            $message = __('Something went wrong, please try again later');
            throw new ClientException($message);
        } finally {
            $log['api_endpoint'] = $this->getApiEndpoint();
            $log['proxy_info'] = $this->getProxyInfo();
            $log['response'] = $response;
            $this->paymentDataLogger->debug($log);
        }

        return $response;
    }

    /**
     * Get API Client
     *
     * @return \Synchrony\Gateway\Client
     */
    protected function getApiClient()
    {
        if ($this->apiClient === null) {
            $storeId = isset($this->clientConfig[StoreIdDataBuilder::STORE_ID_KEY])
                ? $this->clientConfig[StoreIdDataBuilder::STORE_ID_KEY] : null;
            $this->apiClient = $this->apiClientFactory->create($storeId);
        }
        return $this->apiClient;
    }

    /**
     * Process API client exception
     *
     * @param \Exception $e
     * @param string $logMessage
     * @return $this
     */
    protected function processClientException(\Exception $e, $logMessage = null)
    {
        $logMessage = $logMessage ? $logMessage . ': ' . $e->getMessage() : $e->getMessage();
        if ($e instanceof \Synchrony\Gateway\Client\Exception) {
            $logMessage .= '. API Endpoint was: '.$this->getApiEndpoint()
                . '. Response body was: ' . $e->getResponseBody();
        }
        $this->logger->critical($logMessage);
        return $this;
    }

    /**
     * Get API Endpoint
     *
     * @return string
     */
    abstract public function getApiEndpoint();

    /**
     * Process http request
     * @param array $data
     * @return array
     */
    abstract protected function process(array $data);

    /**
     * Get API Proxy Info
     *
     * @return array|null
     */
    protected function getProxyInfo()
    {
        return empty($this->apiClient->getProxy()) ? 'Proxy Not Enabled' : $this->apiClient->getProxy();
    }
}
