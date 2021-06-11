<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.1.0
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SizeLimiterConfig
{
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     *
     * @return string
     */
    public function getDisplayMode($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/size_limiter/display_mode',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     *
     * @return int
     */
    public function getLinkLimit($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/size_limiter/link_limit',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }


    /**
     * @param null|int|\Magento\Store\Model\Store $store
     *
     * @return int
     */
    public function getScrollHeight($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/size_limiter/scroll_height',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     *
     * @return string
     */
    public function getTextLess($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/size_limiter/text_less',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     *
     * @return string
     */
    public function getTextMore($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/size_limiter/text_more',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
