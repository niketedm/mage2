<?php

namespace Synchrony\DigitalBuy\Model\Rule\Condition;

class Order extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_directoryCountry;

    /**
     * @var \Magento\Directory\Model\Config\Source\Allregion
     */
    protected $_directoryAllregion;

    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods
     */
    protected $_shippingAllmethods;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Directory\Model\Config\Source\Country $directoryCountry
     * @param \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion
     * @param \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_directoryCountry = $directoryCountry;
        $this->_directoryAllregion = $directoryAllregion;
        $this->_shippingAllmethods = $shippingAllmethods;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'base_subtotal' => __('Subtotal'),
            'base_grand_total' => __('Grand Total'),
            'total_qty_ordered' => __('Total Items Quantity'),
            'weight' => __('Total Weight'),
            'shipping_method' => __('Shipping Method'),
            'postcode' => __('Shipping Postcode'),
            'region' => __('Shipping Region'),
            'region_id' => __('Shipping State/Province'),
            'country_id' => __('Shipping Country'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Get attribute element
     *
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal':
            case 'base_grand_total':
            case 'weight':
            case 'total_qty_ordered':
                return 'numeric';

            case 'shipping_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }
        return 'string';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }
        return 'text';
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = $this->_directoryCountry->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_directoryAllregion->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = $this->_shippingAllmethods->toOptionArray();
                    break;

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $address */
        if ($model->getIsVirtual()) {
            $address = $model->getBillingAddress();
        } else {
            $address = $model->getShippingAddress();
        }

        switch ($this->getAttribute()) {
            case 'country_id':
                $model->setData('country-id', $address->getCountryId());
                break;
            case 'region_id':
                $model->setData('region_id', $address->getRegionId());
                break;
            case 'region':
                $model->setData('region', $address->getRegion());
                break;
            case 'postcode':
                $model->setData('postcode', $address->getPostcode());
                break;
        }

        return parent::validate($model);
    }
}
