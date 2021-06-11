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

class HighlightConfig
{
    const DEFAULT_COLOR = "#ff5501";

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param mixed $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/highlight/is_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return string
     */
    public function getColor($store = null)
    {
        $color = $this->scopeConfig->getValue(
            'mst_nav/highlight/color',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $color ? $color : self::DEFAULT_COLOR;
    }
}
