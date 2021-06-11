<?php

namespace Synchrony\DigitalBuy\Gateway\Command\Refund;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

class RefundCalculator
{
    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    private $mathCalculatorFactory;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @var OrderPaymentInterface
     */
    private $payment;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Math\CalculatorFactory $mathCalculatorFactory
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
     * Calculates Refund amount for each item and distributes the extra positive amounts to each item and
     * aggregates it by promotion code applied
     *
     * @param OrderPaymentInterface $payment
     * @return array
     */
    public function collectRefundPromoCodes($payment)
    {
        $this->payment = $payment;
        $this->paymentAdditionalInfoManager->setPayment($payment);
        $refundAmounts = $this->getPromoAmountsFromCm();
        $this->validateRefundAmounts($refundAmounts);
        $refundAmounts = $this->fixPromoDistributions($refundAmounts);

        return $refundAmounts;
    }

    /**
     * Calculate promo code amounts based on creditmemo data
     *
     * @return array
     */
    private function getPromoAmountsFromCm()
    {
        $result = [];
        $creditmemo = $this->payment->getCreditmemo();
        $orderItemPromoAmounts = $this->paymentAdditionalInfoManager->getItemPromoAmounts();
        $totalItemAmount = 0;
        foreach ($creditmemo->getAllItems() as $item) {
            if (isset($orderItemPromoAmounts[$item->getOrderItemId()])) {
                $promoCode = $orderItemPromoAmounts[$item->getOrderItemId()]['promo_code'];
                $result[$promoCode] = isset($result[$promoCode])
                    ? $result[$promoCode] + $item->getBaseRowTotal() : $item->getBaseRowTotal();
                $totalItemAmount += $item->getBaseRowTotal();
            }
        }

        $extraAmount = $creditmemo->getBaseGrandTotal() - $totalItemAmount;
        if (abs($extraAmount) > 0.0001 && $totalItemAmount > 0.0001) {
            $this->distributeExtraAmount($result, $extraAmount);
        }

        return $result;
    }

    /**
     * @param array $promoAmounts
     * @throws LocalizedException
     */
    private function validateRefundAmounts($promoAmounts)
    {
        $paidPromoAmounts = $this->paymentAdditionalInfoManager->getPaidPromoAmounts();
        $refundedPromoAmounts = $this->paymentAdditionalInfoManager->getRefundedPromoAmounts();
        $totalPaid = 0;
        $totalRefunded = 0;
        $totalToRefund = 0;
        foreach ($promoAmounts as $promoCode => $amount) {
            if (isset($paidPromoAmounts[$promoCode])) {
                $totalPaid += $paidPromoAmounts[$promoCode];
            } else {
                throw new LocalizedException(__('Unable to refund promo code %1 as it\'s not yet paid', $promoCode));
            }
            if (isset($refundedPromoAmounts[$promoCode])) {
                $totalRefunded += $refundedPromoAmounts[$promoCode];
            }
            $totalToRefund += $amount;
        }

        if ($totalToRefund < 0.0001) {
            throw new LocalizedException(__('Unable to calculate promo code refund amount'));
        }

        if ($totalToRefund > ($totalPaid - $totalRefunded)) {
            $order = $this->payment->getOrder();
            throw new LocalizedException(__(
                'Refund amount exceeds the limit for affected promo codes.'
                    . ' Available amount to refund: %1',
                $order->formatBasePrice($totalPaid - $totalRefunded)
            ));
        }
    }

    /**
     * Check promo code caps and redistribute extra amounts
     *
     * @param $refundAmounts
     * @return array
     */
    private function fixPromoDistributions($refundAmounts)
    {
        $result = [];
        $paidPromoAmounts = $this->paymentAdditionalInfoManager->getPaidPromoAmounts();
        $refundedPromoAmounts = $this->paymentAdditionalInfoManager->getRefundedPromoAmounts();
        foreach ($refundAmounts as $promoCode => $amount) {
            $availableAmount = $paidPromoAmounts[$promoCode];
            if (isset($refundedAmounts[$promoCode])) {
                $availableAmount -= $refundedAmounts[$promoCode];
            }

            $extraAmount = $amount - $availableAmount;
            if ($extraAmount > 0.0001) {
                $result[$promoCode] = $availableAmount;
                unset($refundAmounts[$promoCode]);
                $this->distributeExtraAmount($refundAmounts, $extraAmount);
                $result = array_replace($result, $this->fixPromoDistributions($refundAmounts));
                break;
            } else {
                $result[$promoCode] = $amount;
            }
        }

        return $result;
    }

    /**
     * Distribute extra amount among promo codes
     *
     * @param $refundAmounts
     * @param $extraAmount
     */
    private function distributeExtraAmount(&$refundAmounts, $extraAmount)
    {
        $rounder = $this->mathCalculatorFactory->create();
        $totalItemAmount = array_sum($refundAmounts);
        $ratio = $extraAmount / $totalItemAmount;
        foreach ($refundAmounts as &$promoCodeAmount) {
            $promoCodeAmount += $rounder->deltaRound($promoCodeAmount * $ratio);
        }
    }
}
