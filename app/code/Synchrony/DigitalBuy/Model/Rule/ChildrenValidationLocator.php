<?php

namespace Synchrony\DigitalBuy\Model\Rule;

/**
 * Class ChildrenValidationLocator
 *
 * Used to determine necessity to validate rule on item's children that may depends on product type
 */
class ChildrenValidationLocator
{
    /**
     * @var array
     */
    private $productTypeChildrenValidationMap;

    /**
     * @param array $productTypeChildrenValidationMap
     * <pre>
     * [
     *      'ProductType1' => true,
     *      'ProductType2' => false
     * ]
     * </pre>
     */
    public function __construct(
        array $productTypeChildrenValidationMap = []
    ) {
        $this->productTypeChildrenValidationMap = $productTypeChildrenValidationMap;
    }

    /**
     * Checks necessity to validate rule on item's children
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return bool
     */
    public function isChildrenValidationRequired(\Magento\Sales\Api\Data\OrderItemInterface $item): bool
    {
        $type = $item->getProduct()->getTypeId();
        if (isset($this->productTypeChildrenValidationMap[$type])) {
            return (bool)$this->productTypeChildrenValidationMap[$type];
        }
        return true;
    }
}
