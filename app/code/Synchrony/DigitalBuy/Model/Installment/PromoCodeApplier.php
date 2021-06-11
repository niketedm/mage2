<?php

namespace Synchrony\DigitalBuy\Model\Installment;

use \Magento\Sales\Model\Order;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

class PromoCodeApplier
{
    /**
     * @var \Magento\Framework\Math\Calculator
     */
    private $rounder;

    /**
     * @var string
     */
    private $promoCode;

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    private $mathCalculatorFactory;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @param \Magento\Framework\Math\CalculatorFactory $mathCalculatorFactory,
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     */
    public function __construct(
        \Magento\Framework\Math\CalculatorFactory $mathCalculatorFactory,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager
    ) {
        $this->mathCalculatorFactory = $mathCalculatorFactory;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
    }

    /**
     * Apply Promo Code on order and split promotional Amount on to each order item
     *
     * @param Order $order
     * @param int|string $installmentPromoCode
     * @return void
     */
    public function applyPromoCode(Order $order, $installmentPromoCode)
    {
        $this->rounder = $this->mathCalculatorFactory->create();
        $this->promoCode = $installmentPromoCode;
        $itemAmounts = $this->calculateItemAmounts($order);

        /* Aggregate item amount to promo codes */
        $promoTotals = [];
        $promoTotals[$installmentPromoCode] = (float) $order->getBaseGrandTotal();

        $ruleMetaData = [[
            'name' => __('Returned by Synchrony'),
            'promo_code' => $installmentPromoCode
        ]];

        $this->paymentAdditionalInfoManager->setPayment($order->getPayment())
            ->setPromoAmounts($promoTotals)
            ->setItemPromoAmounts($itemAmounts)
            ->setRuleMetadata($ruleMetaData);
    }

    /**
     * Split promotion amounts per order item
     * returns 2 dimensional array of the following structure
     * [
     *     $orderItemId => [
     *         'promo_code' => $promoCode,
     *         'amount' => $amount
     *     ]
     * ]
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    private function calculateItemAmounts(\Magento\Sales\Model\Order $order)
    {
        $itemAmounts = [];
        $itemAmountsTotal = 0;
        foreach ($order->getAllItems() as $item) {
            if (($item->getChildrenItems() && $item->isChildrenCalculated())
                || ($item->hasParentItem() && !$item->getParentItem()->isChildrenCalculated())
                || $item->getBaseRowTotal() < 0.0001) {
                continue;
            }

            $itemAmounts[$item->getId()] = [
                'promo_code' => $this->promoCode,
                'amount' => $item->getBaseRowTotal()
            ];
            $itemAmountsTotal += $item->getBaseRowTotal();
        }

        $orderGrandTotal = $order->getBaseGrandTotal();
        $extraAmount = $orderGrandTotal - $itemAmountsTotal;
        if (abs($extraAmount) > 0.0001 && $itemAmountsTotal > 0.0001) {
            $ratio = $extraAmount / $itemAmountsTotal;
            foreach ($itemAmounts as &$item) {
                $item['amount'] += $this->rounder->deltaRound($item['amount'] * $ratio);
            }
        }
        return $itemAmounts;
    }
}
