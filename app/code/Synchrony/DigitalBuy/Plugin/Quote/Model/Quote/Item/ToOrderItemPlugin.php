<?php

namespace Synchrony\DigitalBuy\Plugin\Quote\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as AbstractQuoteItem;

/**
 * Plugin for Magento\Quote\Model\Quote\Item\ToOrderItem
 */
class ToOrderItemPlugin
{
    /**
     * Transfer product object from quote to order
     *
     * @param QuoteToOrderItem $subject
     * @param OrderItemInterface $result
     * @param AbstractQuoteItem $item
     * @param array $data
     * @return OrderItemInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConvert(
        QuoteToOrderItem $subject,
        OrderItemInterface $result,
        AbstractQuoteItem $item,
        $data = []
    ) {
        if ($item->hasProduct() && !$result->hasProduct()) {
            $result->setProduct($item->getProduct());
        }

        return $result;
    }
}
