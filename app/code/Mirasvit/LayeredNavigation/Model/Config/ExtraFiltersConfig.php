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
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;

class ExtraFiltersConfig
{
    const NEW_FILTER                      = 'mst_new_products';
    const ON_SALE_FILTER                  = 'mst_on_sale';
    const STOCK_FILTER                    = 'mst_stock_status';
    const IN_STOCK_FILTER                 = 2;
    const OUT_OF_STOCK_FILTER             = 1;
    const RATING_FILTER                   = 'rating_summary';
    const NEW_FILTER_FRONT_PARAM          = 'mst_new_products';
    const ON_SALE_FILTER_FRONT_PARAM      = 'mst_on_sale';
    const STOCK_FILTER_FRONT_PARAM        = 'mst_stock';
    const RATING_FILTER_FRONT_PARAM       = 'rating';
    const NEW_FILTER_DEFAULT_LABEL        = 'New';
    const ON_SALE_FILTER_DEFAULT_LABEL    = 'Sale';
    const STOCK_FILTER_DEFAULT_LABEL      = 'Stock';
    const RATING_FILTER_DEFAULT_LABEL     = 'Rating';
    const RATING_FILTER_DATA              = 'm__rating_filter_data';
    const RATING_DATA
                                          = [
            5 => 100,
            4 => 80,
            3 => 60,
            2 => 40,
            1 => 20,
        ];
    const STOCK_FILTER_IN_STOCK_LABEL     = 'instock';
    const STOCK_FILTER_OUT_OF_STOCK_LABEL = 'outofstock';
    const RATING_FILTER_ONE_LABEL         = 'rating1';
    const RATING_FILTER_TWO_LABEL         = 'rating2';
    const RATING_FILTER_THREE_LABEL       = 'rating3';
    const RATING_FILTER_FOUR_LABEL        = 'rating4';
    const RATING_FILTER_FIVE_LABEL        = 'rating5';

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
     * @param mixed $filter
     * @param mixed $store
     *
     * @return mixed
     */
    public function isFilterEnabled($filter, $store = null)
    {
        $method = 'is' . $this->transformToMethod($filter) . 'FilterEnabled';
        if (!method_exists($this, $method)) {
            throw new LocalizedException(__('Filter type "%1" does not exist', $filter));
        }

        return $this->{$method}($store);
    }

    /**
     * @param mixed    $filter
     * @param int|null $store
     *
     * @return int
     * @throws LocalizedException
     */
    public function getFilterPosition($filter, $store = null)
    {
        $method = 'get' . $this->transformToMethod($filter) . 'FilterPosition';

        if (!method_exists($this, $method)) {
            throw new LocalizedException(__('Filter type "%1" does not exist', $filter));
        }

        return $this->{$method}($store);
    }

    /**
     * Transform given str to Upper Camel Case compatible string for use in method.
     *
     * @param string $str
     *
     * @return string
     */
    private function transformToMethod($str)
    {
        return str_replace('_', '', ucwords($str, '_'));
    }

    /**
     * @param mixed $store
     *
     * @return bool
     */
    public function isNewFilterEnabled($store = null)
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_nav/extra_filter/new/is_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return string
     */
    public function getNewFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/extra_filter/new/label',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return int
     */
    public function getNewFilterPosition($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            'mst_nav/extra_filter/new/position',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return bool
     */
    public function isOnSaleFilterEnabled($store = null)
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_nav/extra_filter/sale/is_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return string
     */
    public function getOnSaleFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/extra_filter/sale/label',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return int
     */
    public function getOnSaleFilterPosition($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            'mst_nav/extra_filter/sale/position',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return bool
     */
    public function isStockFilterEnabled($store = null)
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_nav/extra_filter/stock/is_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return string
     */
    public function getStockFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/extra_filter/stock/label',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return string
     */
    public function getInStockFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/extra_filter/stock/label_in',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return string
     */
    public function getOutOfStockFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/extra_filter/stock/label_out',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int $store
     *
     * @return int
     */
    public function getStockFilterPosition($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            'mst_nav/extra_filter/stock/position',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return bool
     */
    public function isRatingFilterEnabled($store = null)
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_nav/extra_filter/rating/is_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return string
     */
    public function getRatingFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'mst_nav/extra_filter/rating/label',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     *
     * @return int
     */
    public function getRatingFilterPosition($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            'mst_nav/extra_filter/rating/position',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
