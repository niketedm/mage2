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
use Mirasvit\LayeredNavigation\Model\Config\Source\HorizontalFilterOptions;

class HorizontalBarConfig
{
    const STATE_BLOCK_NAME            = 'catalog.navigation.state';
    const STATE_SEARCH_BLOCK_NAME     = 'catalogsearch.navigation.state';
    const STATE_HORIZONTAL_BLOCK_NAME = 'm.catalog.navigation.horizontal.state';
    const FILTER_BLOCK_NAME           = 'm.catalog.navigation.horizontal.renderer';

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
     * @param mixed|null $store
     *
     * @return string[]|string
     */
    public function getFilters($store = null)
    {
        $filters = $this->scopeConfig->getValue(
            'mst_nav/horizontal_bar/filters',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (!$filters) {
            return $filters;
        }

        if (strpos($filters, HorizontalFilterOptions::ALL_FILTERED_ATTRIBUTES) !== false) {
            return HorizontalFilterOptions::ALL_FILTERED_ATTRIBUTES;
        }

        $result = array_map(
            function ($value) {
                return strtok($value, HorizontalFilterOptions::HORIZONTAL_FILTER_CONFIG_SEPARATOR);
            },
            explode(',', $filters)
        );

        $result = array_unique($result);

        return $result;
    }

    /**
     * @param mixed $store
     *
     * @return mixed
     */
    public function getHideHorizontalFiltersValue($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/horizontal_bar/horizontal_filters_hide',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return mixed
     */
    public function isUseCatalogLeftnavHorisontalNavigation($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/horizontal_bar/is_use_catalog_leftnav_horisontal_navigation',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
