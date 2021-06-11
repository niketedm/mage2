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



namespace Mirasvit\LayeredNavigation\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const AJAX_PRODUCT_LIST_WRAPPER_ID = 'm-navigation-product-list-wrapper';
    const NAV_IMAGE_REG_PRODUCT_DATA   = 'm-navigation-register-product-data';

    const NAV_REPLACER_TAG = '<div id="m-navigation-replacer"></div>'; //use for filter opener

    const IS_CATALOG_SEARCH = 'catalogsearch';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isSeoFiltersEnabled()
    {
        return (bool)$this->scopeConfig->getValue('seofilter/seofilter/is_seofilter_enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return (bool)$this->scopeConfig->getValue('mst_nav/general/is_ajax_enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getApplyingMode()
    {
        return $this->scopeConfig->getValue('mst_nav/general/filter_applying_mode', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isShowNestedCategories()
    {
        return (bool)$this->scopeConfig->getValue('mst_nav/general/is_show_nested_categories', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isMultiselectEnabled()
    {
        return (bool)$this->scopeConfig->getValue('mst_nav/general/is_multiselect_enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getFilterItemDisplayMode()
    {
        return $this->scopeConfig->getValue('mst_nav/general/filter_item_display_mode', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getDisplayOptionsBackgroundColor()
    {
        return $this->scopeConfig->getValue('mst_nav/general/display_options_background_color', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getDisplayOptionsBorderColor()
    {
        return $this->scopeConfig->getValue('mst_nav/general/display_options_border_color', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getDisplayOptionsCheckedLabelColor()
    {
        return $this->scopeConfig->getValue('mst_nav/general/display_options_checked_label_color', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isOpenFilter()
    {
        return $this->scopeConfig->getValue('mst_nav/general/is_open_filter', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isCorrectElasticFilterCount()
    {
        return $this->scopeConfig->getValue('mst_nav/general/is_correct_elastic_filter_count', ScopeInterface::SCOPE_STORE);
    }

    public function getSearchEngine()
    {
        return $this->scopeConfig->getValue('catalog/search/engine', ScopeInterface::SCOPE_STORE);
    }
}
