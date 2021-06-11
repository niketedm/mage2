<?php
namespace Synchrony\DigitalBuy\Model\Source\DynamicPricing;

use Magento\Framework\Option\ArrayInterface;

class Strategy implements ArrayInterface
{
    const STATIC = 1;

    const DYNAMIC = 2;

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::STATIC,
                'label' => __('Value prop message'),
            ],
            [
                'value' => self::DYNAMIC,
                'label' => __('Monthly payment message')
            ]
        ];
    }
}
