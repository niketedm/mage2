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

class StateBarConfig
{
    /**
     * @var ScopeConfigInterface
     */
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
    public function isHorizontalPosition($store = null)
    {
        return $this->scopeConfig->getValue(
                'mst_nav/state_bar/position',
                ScopeInterface::SCOPE_STORE,
                $store
            ) === 'horizontal';
    }

    /**
     * @param mixed $store
     *
     * @return bool
     */
    public function isHidden($store = null)
    {
        return $this->scopeConfig->getValue(
                'mst_nav/state_bar/position',
                ScopeInterface::SCOPE_STORE,
                $store
            ) === 'hidden';
    }

    /**
     * @param mixed $store
     *
     * @return bool
     */
    public function isFilterClearBlockInOneRow($store = null)
    {
        return $this->scopeConfig->getValue(
                'mst_nav/state_bar/group_mode',
                ScopeInterface::SCOPE_STORE,
                $store
            ) === 'group';
    }
}
