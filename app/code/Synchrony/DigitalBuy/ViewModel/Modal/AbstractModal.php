<?php

namespace Synchrony\DigitalBuy\ViewModel\Modal;

use Synchrony\DigitalBuy\Gateway\Config\AbstractPaymentConfig as SynchronyConfig;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Registry;

abstract class AbstractModal implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Test static assets base URL
     */
    const TEST_STATIC_ASSETS_BASE_URL = 'https://ubuy.syf.com/';

    /**
     * Prod static assets base URL
     */
    const PROD_STATIC_ASSETS_BASE_URL = 'https://buy.syf.com/';

    /**
     * Registry key to store token
     */
    const TOKEN_REGISTRY_KEY = 'synchrony_digitalbuy_token';

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var SynchronyConfig
     */
    protected $synchronyConfig;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Modal constructor.
     * @param SynchronyConfig $synchronyConfig
     * @param Registry $coreRegistry
     * @param DateTime $dateTime
     */
    public function __construct(
        SynchronyConfig $synchronyConfig,
        Registry $coreRegistry,
        DateTime $dateTime
    ) {
        $this->synchronyConfig = $synchronyConfig;
        $this->coreRegistry = $coreRegistry;
        $this->dateTime = $dateTime;
    }

    /**
     * Retrieve Client Token from registry
     *
     * @return string
     */
    public function getDigitalBuyTokenFromRegistry()
    {
        return $this->coreRegistry->registry(static::TOKEN_REGISTRY_KEY);
    }

    /**
     * Save token to registry
     *
     * @param $token
     * @return $this
     */
    public function setDigitalBuyTokenToRegistry($token)
    {
        $this->coreRegistry->unregister(static::TOKEN_REGISTRY_KEY);
        $this->coreRegistry->register(static::TOKEN_REGISTRY_KEY, $token);
        return $this;
    }

    /**
     * Retrieve store id
     *
     * @return null|int
     */
    abstract protected function getStoreId();

    /**
     * Retrieve merchant id
     *
     * @return string
     */
    public function getDigitalBuyMerchantId()
    {
        return $this->synchronyConfig->getDigitalBuyApiMerchantId($this->getStoreId());
    }

    /**
     * Retrieve current timestamp
     *
     * @return int
     */
    public function getCurrentTimestamp()
    {
        return $this->dateTime->gmtTimestamp();
    }

    /**
     * Retrieve base URL for Digital Buy static assets
     *
     * @return string
     */
    public function getStaticAssetsBaseUrl()
    {
        return $this->synchronyConfig->getIsSandboxMode($this->getStoreId())
            ? self::TEST_STATIC_ASSETS_BASE_URL : self::PROD_STATIC_ASSETS_BASE_URL;
    }
}
