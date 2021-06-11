<?php

namespace Mancini\ProductConsole\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /** @var StoreManagerInterface */
    protected $_storeManager;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $_scopeConfig;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param $path
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getStoreConfigValue($path)
    {
        $store = $this->_storeManager->getStore()->getId();
        return $this->_scopeConfig->getValue(
            $path, ScopeInterface::SCOPE_STORE, $store
        );
    }
}
