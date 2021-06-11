<?php

namespace Mancini\Sort\Block\Product\ProductList;

use Magento\Framework\Data\Collection;
//use Magento\Tests\NamingConvention\true\string;

/**
 * Product list toolbar
 *
 * @method setModuleName(string $moduleName)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * Set collection to pager
     *
     * @param Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only when the passed value is integer and greater that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            if (in_array($this->getCurrentOrder(), array('low_price', 'high_price'))) {
                if ($this->getCurrentOrder() == 'low_price') {
                    $this->_collection->setOrder('price', 'asc');
                } else {
                    $this->_collection->setOrder('price', 'desc');
                }
            } else {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }

        }
        return $this;
    }


    /**
     * Retrieve available Order fields list
     *
     * @return array
     */
    public function getAvailableOrders()
    {
        $this->loadAvailableOrders();
        if (isset($this->_availableOrder['price'])) {
            unset($this->_availableOrder['price']);
            $this->_availableOrder['low_price'] = __('Price (Low to High)');
            $this->_availableOrder['high_price'] = __('Price (High to Low)');
        }
        return $this->_availableOrder;
    }

    /**
     * Load Available Orders
     *
     * @return $this
     */
    private function loadAvailableOrders()
    {
        if ($this->_availableOrder === null) {
            $this->_availableOrder = $this->_catalogConfig->getAttributeUsedForSortByArray();
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Catalog\Block\Product\ProductList\Toolbar'));
        return parent::_toHtml();
    }
}
