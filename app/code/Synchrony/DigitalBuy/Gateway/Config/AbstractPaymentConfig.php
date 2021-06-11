<?php

namespace Synchrony\DigitalBuy\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

abstract class AbstractPaymentConfig extends \Magento\Payment\Gateway\Config\Config
{
    /**
     * Payment method code.
     *
     * @var string
     */
    const METHOD_CODE = '';

    /**
     * Allowed base currency
     */
    const ALLOWED_CURRENCY = 'USD';

    /**
     * @var CommonConfig
     */
    protected $commonConfig;

    /**
     * @var array
     */
    private $commonConfigKeys = ['allowspecific', 'specificcountry', 'debug'];

    /**
     * AbstractPaymentConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param CommonConfig $commonConfig
     * @param null|string $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CommonConfig $commonConfig,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        if ($methodCode === null) {
            $methodCode = static::METHOD_CODE;
        }
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->commonConfig = $commonConfig;
    }

    /**
     * Retrieve module version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->commonConfig->getModuleVersion();
    }

    /**
     * Get payment method allowed currencies
     *
     * @return array
     */
    public function getAllowedCurrency()
    {
        return [self::ALLOWED_CURRENCY];
    }

    /**
     * Retrieve payment action
     *
     * @param string|intnull $storeId
     * @return string
     */
    public function getPaymentAction($storeId = null)
    {
        return $this->getValue('payment_action', $storeId);
    }

    /**
     * Retrieve Digitalbuy Authentication API Endpoint
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getAuthenticationApiEndpoint($storeId = null)
    {
        return $this->commonConfig->getAuthenticationApiEndpoint($storeId);
    }

    /**
     * Retrieve Digitalbuy Status Inquiry API Endpoint
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getStatusInquiryApiEndpoint($storeId = null)
    {
        return $this->commonConfig->getStatusInquiryApiEndpoint($storeId);
    }

    /**
     * Retrieve Digitalbuy Capture API Endpoint
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getCaptureApiEndpoint($storeId = null)
    {
        return $this->commonConfig->getCaptureApiEndpoint($storeId);
    }

    /**
     * Retrieve Buy Service API Endpoint
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getBuyServiceApiEndpoint($storeId = null)
    {
        return $this->commonConfig->getBuyServiceApiEndpoint($storeId);
    }

    /**
     * Retrieve Merchant ID to be used in Digital Buy API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    abstract public function getDigitalBuyApiMerchantId($storeId = null);

    /**
     * Retrieve Digital Buy API password
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    abstract public function getDigitalBuyApiPassword($storeId = null);

    /**
     * Retrieve Buy Service API Username for API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getBuyServiceApiUsername($storeId = null)
    {
        return $this->commonConfig->getBuyServiceApiUsername($storeId);
    }

    /**
     * Retrieve Proxy Enable flag
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getIsApiProxyEnabled($storeId = null)
    {
        return $this->commonConfig->getIsApiProxyEnabled($storeId);
    }

    /**
     * Retrieve Proxy Host
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getApiProxyHost($storeId = null)
    {
        return $this->commonConfig->getApiProxyHost($storeId);
    }

    /**
     * Retrieve Proxy Port
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getApiProxyPort($storeId = null)
    {
        return $this->commonConfig->getApiProxyPort($storeId);
    }

    /**
     * Retrieve Buy Service API Password for API calls
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getBuyServiceApiPassword($storeId = null)
    {
        return $this->commonConfig->getBuyServiceApiPassword($storeId);
    }

    /**
     * Retrieve Merchant ID to be used in Buy Service API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getBuyServiceApiMerchantId($storeId = null)
    {
        $merchantId = $this->commonConfig->getBuyServiceApiMerchantId($storeId);
        return $merchantId ?: $this->getDigitalBuyApiMerchantId($storeId);
    }

    /**
     * Retrieve Client value to be used in Buy Service API calls
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getBuyServiceClient($storeId = null)
    {
        return $this->commonConfig->getBuyServiceClient($storeId);
    }

    /**
     * Retrieve Partner Code to be used in Buy Service API calls
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getBuyServicePartnerCode($storeId = null)
    {
        return $this->commonConfig->getBuyServicePartnerCode($storeId);
    }

    /**
     * Retrieve APi request timeout
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getApiTimeout($storeId = null)
    {
        return $this->commonConfig->getApiTimeout($storeId);
    }

    /**
     * Retrieve sandbox flag
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getIsSandboxMode($storeId = null)
    {
        return $this->commonConfig->getIsSandboxMode($storeId);
    }

    /**
     * Retrieve debug flag
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getIsDebugOn($storeId = null)
    {
        return $this->commonConfig->getIsDebugOn($storeId);
    }

    /**
     * Retrieve address type to use as a data source for Digital Buy modals initialization
     *
     * @param string|intnull $storeId
     * @return string
     */
    public function getAddressTypeToPass($storeId = null)
    {
        return $this->commonConfig->getAddressTypeToPass($storeId);
    }

    /**
     * Retrieve show address match note flag
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getCanShowAddressMatchNote($storeId = null)
    {
        return $this->commonConfig->getCanShowAddressMatchNote($storeId);
    }

    /**
     * Retrieve token expiration interval (in seconds)
     * Approximate ceiled value for internal check, just to miniumize redundant API calls when we know for sure that
     * token has expired
     *
     * @return int
     */
    public function getTokenExpInterval()
    {
        return $this->commonConfig->getTokenExpInterval();
    }

    /**
     * Retrieve prohibited product attribute set ids
     *
     * @param string|int|null $storeId
     * @return array
     */
    public function getProhibitedProductAttributeSets($storeId = null)
    {
        return $this->commonConfig->getProhibitedProductAttributeSets($storeId);
    }

    /**
     * Get config value
     *
     * @param string $field
     * @param string|intnull $storeId
     * @return mixed
     */
    public function getValue($field, $storeId = null)
    {
        if (in_array($field, $this->commonConfigKeys)) {
            return $this->commonConfig->getValue($field, $storeId);
        }
        return parent::getValue($field, $storeId);
    }
}
