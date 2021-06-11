<?php
namespace Synchrony\DigitalBuy\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\ModuleResource;
use Magento\Store\Model\ScopeInterface;

class CommonConfig
{
    /**
     * Config path prefix
     */
    const CONFIG_PREFIX = 'payment/synchrony_digitalbuy/';

    /**
     * Module version cache key
     */
    const MODULE_VERSION_CACHE_KEY = 'synchrony_digitalbuy_module_version';

    /**
     * @var ModuleResource
     */
    private $moduleResource;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    private $configCache;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CommonConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleResource $moduleResource
     * @param \Magento\Framework\App\Cache\Type\Config $configCache
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ModuleResource $moduleResource,
        \Magento\Framework\App\Cache\Type\Config $configCache
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleResource = $moduleResource;
        $this->configCache = $configCache;
    }

    /**
     * Retrieve module version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleVersion = $this->configCache->load(self::MODULE_VERSION_CACHE_KEY);
        if (!$moduleVersion) {
            $moduleVersion = $this->moduleResource->getDbVersion('Synchrony_DigitalBuy');
            $this->configCache->save($moduleVersion, self::MODULE_VERSION_CACHE_KEY);
        }
        return $moduleVersion;
    }

    /**
     * Retrieve Digitalbuy Authentication API Endpoint
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getAuthenticationApiEndpoint($storeId = null)
    {
        $endpoint = $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_authentication_api_endpoint', $storeId)
            : $this->getValue('production_authentication_api_endpoint', $storeId);
        return trim($endpoint);
    }

    /**
     * Retrieve Digitalbuy Capture API Endpoint
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getCaptureApiEndpoint($storeId = null)
    {
        $endpoint = $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_capture_api_endpoint', $storeId)
            : $this->getValue('production_capture_api_endpoint', $storeId);
        return trim($endpoint);
    }

    /**
     * Retrieve Digitalbuy Status Inquiry API Endpoint
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getStatusInquiryApiEndpoint($storeId = null)
    {
        $endpoint = $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_status_inquiry_api_endpoint', $storeId)
            : $this->getValue('production_status_inquiry_api_endpoint', $storeId);
        return trim($endpoint);
    }

    /**
     * Retrieve Buy Service API Endpoint
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getBuyServiceApiEndpoint($storeId = null)
    {
        $endpoint = $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_buyservice_api_endpoint', $storeId)
            : $this->getValue('production_buyservice_api_endpoint', $storeId);
        return trim($endpoint);
    }

    /**
     * Retrieve Merchant ID to be used in Digital Buy API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getDigitalBuyApiMerchantId($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_digitalbuy_api_merchant_id', $storeId)
            : $this->getValue('production_digitalbuy_api_merchant_id', $storeId);
    }

    /**
     * Retrieve Digital Buy API password
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getDigitalBuyApiPassword($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_digitalbuy_api_password', $storeId)
            : $this->getValue('production_digitalbuy_api_password', $storeId);
    }

    /**
     * Retrieve Buy Service API Username for API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getBuyServiceApiUsername($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_buyservice_api_username', $storeId)
            : $this->getValue('production_buyservice_api_username', $storeId);
    }

    /**
     * Retrieve Buy Service API Password for API calls
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getBuyServiceApiPassword($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_buyservice_api_password', $storeId)
            : $this->getValue('production_buyservice_api_password', $storeId);
    }

    /**
     * Retrieve Merchant ID to be used in Buy Service API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getBuyServiceApiMerchantId($storeId = null)
    {
        $merchantId = $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_buyservice_api_merchant_id', $storeId)
            : $this->getValue('production_buyservice_api_merchant_id', $storeId);
        return $merchantId;
    }

    /**
     * Retrieve Proxy Enable flag
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getIsApiProxyEnabled($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_api_proxy', $storeId)
            : $this->getValue('production_api_proxy', $storeId);
    }

    /**
     * Retrieve Proxy Host
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getApiProxyHost($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_api_proxy_host', $storeId)
            : $this->getValue('production_api_proxy_host', $storeId);
    }

    /**
     * Retrieve Proxy Port
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getApiProxyPort($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_api_proxy_port', $storeId)
            : $this->getValue('production_api_proxy_port', $storeId);
    }

    /**
     * Retrieve Client value to be used in Buy Service API calls
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getBuyServiceClient($storeId = null)
    {
        return $this->getValue('buyservice_client', $storeId);
    }

    /**
     * Retrieve Partner Code to be used in Buy Service API calls
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getBuyServicePartnerCode($storeId = null)
    {
        return $this->getValue('buyservice_partnercode', $storeId);
    }

    /**
     * Retrieve Installment API Merchant Id for API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getInstallmentApiMerchantId($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_installment_api_merchant_id', $storeId)
            : $this->getValue('production_installment_api_merchant_id', $storeId);
    }

    /**
     * Retrieve Installment API Password for API calls
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getInstallmentApiPassword($storeId = null)
    {
        return $this->getIsSandboxMode($storeId)
            ? $this->getValue('sandbox_installment_api_password', $storeId)
            : $this->getValue('production_installment_api_password', $storeId);
    }

    /**
     * Retrieve APi request timeout
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getApiTimeout($storeId = null)
    {
        return (int)$this->getValue('api_timeout', $storeId);
    }

    /**
     * Retrieve sandbox flag
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getIsSandboxMode($storeId = null)
    {
        return (bool)$this->getValue('sandbox', $storeId);
    }

    /**
     * Retrieve debug flag
     *
     * @param string|int|null $storeId
     * @return bool
     */
    public function getIsDebugOn($storeId = null)
    {
        return (bool)$this->getValue('debug', $storeId);
    }

    /**
     * Retrieve address type to use as a data source for Digital Buy modals initialization
     *
     * @param string|intnull $storeId
     * @return string
     */
    public function getAddressTypeToPass($storeId = null)
    {
        return $this->getValue('address_type_to_pass', $storeId);
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
        return (int)$this->getValue('token_exp_interval');
    }

    /**
     * Retrieve prohibited product attribute set ids
     *
     * @param string|int|null $storeId
     * @return array
     */
    public function getProhibitedProductAttributeSets($storeId = null)
    {
        return explode(',', $this->getValue('prohibited_attribute_sets', $storeId));
    }

    /**
     * Retrieve config value
     *
     * @param string $field
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PREFIX . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
