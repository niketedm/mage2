<?php

namespace Synchrony\DigitalBuy\Model\Rule\Condition;

/**
 * Product rule condition data model
 *
 */
class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Add special attributes
     *
     * @param array $attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['order_item_qty'] = __('Quantity in order');
        $attributes['order_item_price'] = __('Price in order');
        $attributes['order_item_row_total'] = __('Row total in order');
    }

    /**
     * Validate Product Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $model->getProduct();
        if (!$product instanceof \Magento\Catalog\Model\Product) {
            $product = $this->productRepository->getById($model->getProductId());
        }

        $product->setOrderItemQty(
            $model->getQtyOrdered()
        )->setOrderItemPrice(
            $model->getBasePrice()
        )->setOrderItemRowTotal(
            $model->getBaseRowTotal()
        );

        $attrCode = $this->getAttribute();

        if ($attrCode === 'category_ids') {
            return $this->validateAttribute($this->_getAvailableInCategories($product->getId()));
        }

        if ($attrCode === 'order_item_price') {
            $numericOperations = $this->getDefaultOperatorInputByType()['numeric'];
            if (in_array($this->getOperator(), $numericOperations)) {
                $this->setData('value', $this->getFormattedPrice($this->getValue()));
            }
        }

        return parent::validate($product);
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'synchrony_digitalbuy/promotion_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                }
                break;
            default:
                break;
        }
        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }

    /**
     * @param string $value
     * @return float|null
     */
    private function getFormattedPrice($value)
    {
        $value = preg_replace('/[^0-9^\^.,-]/m', '', $value);

        /**
         * If the comma is the third symbol in the number, we consider it to be a decimal separator
         */
        $separatorComa = strpos($value, ',');
        $separatorDot = strpos($value, '.');
        if ($separatorComa !== false && $separatorDot === false && preg_match('/,\d{3}$/m', $value) === 1) {
            $value .= '.00';
        }
        return $this->_localeFormat->getNumber($value);
    }
}
