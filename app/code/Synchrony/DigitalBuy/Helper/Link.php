<?php

namespace Synchrony\DigitalBuy\Helper;

/**
 * Class Link Helper
 */
class Link
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get configuration value by path
     *
     * @param $configPath
     * @return string|bool|int
     */
    public function getConfigValueByPath($configPath)
    {
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
