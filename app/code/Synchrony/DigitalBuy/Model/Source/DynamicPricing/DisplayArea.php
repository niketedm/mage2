<?php
namespace Synchrony\DigitalBuy\Model\Source\DynamicPricing;

use Magento\Framework\Option\ArrayInterface;

class DisplayArea implements ArrayInterface
{
    const PRODUCT_DETAILS_PAGE = 1;
    
    const CHECKOUT_PAGE = 2;
    
    const BOTH_PAGES = 3;

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PRODUCT_DETAILS_PAGE,
                'label' => __('Product Details Page (PDP)'),
            ],
            [
                'value' => self::CHECKOUT_PAGE,
                'label' => __('Checkout')
            ],
            [
                'value' => self::BOTH_PAGES,
                'label' => __('PDP and Checkout')
            ]
        ];
    }
}
