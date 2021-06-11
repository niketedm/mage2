<?php

namespace Synchrony\DigitalBuy\Model\Rule\Condition\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

/**
 * @api
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Synchrony\DigitalBuy\Model\Rule\Condition\Product
     */
    protected $_ruleConditionProd;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Synchrony\DigitalBuy\Model\Rule\Condition\Product $ruleConditionProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Synchrony\DigitalBuy\Model\Rule\Condition\Product $ruleConditionProduct,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_ruleConditionProd = $ruleConditionProduct;
        $this->setType(\Synchrony\DigitalBuy\Model\Rule\Condition\Product\Combine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_ruleConditionProd->loadAttributeOptions()->getAttributeOption();
        $pAttributes = [];
        $iAttributes = [];
        foreach ($productAttributes as $code => $label) {
            if (strpos($code, 'order_item_') === 0) {
                $iAttributes[] = [
                    'value' => \Synchrony\DigitalBuy\Model\Rule\Condition\Product::class . '|' . $code,
                    'label' => $label,
                ];
            } else {
                $pAttributes[] = [
                    'value' => \Synchrony\DigitalBuy\Model\Rule\Condition\Product::class . '|' . $code,
                    'label' => $label,
                ];
            }
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \Synchrony\DigitalBuy\Model\Rule\Condition\Product\Combine::class,
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Order Item Attribute'), 'value' => $iAttributes],
                ['label' => __('Product Attribute'), 'value' => $pAttributes]
            ]
        );
        return $conditions;
    }

    /**
     * Collect validated attributes
     *
     * @param ProductCollection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
