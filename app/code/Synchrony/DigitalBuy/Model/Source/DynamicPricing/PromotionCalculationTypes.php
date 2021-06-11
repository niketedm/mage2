<?php
namespace Synchrony\DigitalBuy\Model\Source\DynamicPricing;

use Magento\Framework\Option\ArrayInterface;

class PromotionCalculationTypes implements ArrayInterface
{
    const EQUAL_PAY_NO_INTEREST_TYPE = 1;

    const DEFFERED_INTEREST_TYPE = 2;

    const REDUCED_APR_TYPE = 3;

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::EQUAL_PAY_NO_INTEREST_TYPE,
                'label' => __('Equal Pay No Interest')
            ],
            [
                'value' => self::DEFFERED_INTEREST_TYPE,
                'label' => __('Deferred Interest')
            ],
            [
                'value' => self::REDUCED_APR_TYPE,
                'label' => __('Reduced APR / Fixed Payment')
            ]
        ];
    }
}
