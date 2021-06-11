<?php

namespace Synchrony\DigitalBuy\Plugin\Sales\Model\ResourceModel\Order;

use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig;
use Synchrony\DigitalBuy\Gateway\Config\InstallmentConfig;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager;

class PaymentPlugin
{
    /**
     * @var AdditionalInfoManager
     */
    private $additionalInfoManager;

    /**
     * PaymentPlugin constructor.
     * @param AdditionalInfoManager $additionalInfoManager
     */
    public function __construct(AdditionalInfoManager $additionalInfoManager)
    {
        $this->additionalInfoManager = $additionalInfoManager;
    }

    /**
     * Check if payment has quote item id based synchrony promo distributions and convert them to be order item id based
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Payment $subject
     * @param \Magento\Sales\Model\Order\Payment $payment
     */
    public function beforeSave(\Magento\Sales\Model\ResourceModel\Order\Payment $subject, $payment)
    {
        if (!in_array($payment->getMethod(), [RevolvingConfig::METHOD_CODE, InstallmentConfig::METHOD_CODE])) {
            return;
        }

        $quoteItemPromoAmounts = $this->additionalInfoManager->setPayment($payment)->getQuoteItemPromoAmounts();
        if (!$quoteItemPromoAmounts) {
            return;
        }

        $orderItemPromoAmounts = [];
        foreach ($payment->getOrder()->getAllItems() as $item) {
            if (isset($quoteItemPromoAmounts[$item->getQuoteItemId()])) {
                $orderItemPromoAmounts[$item->getId()] = $quoteItemPromoAmounts[$item->getQuoteItemId()];
            }
        }

        $this->additionalInfoManager->setItemPromoAmounts($orderItemPromoAmounts)
            ->unsQuoteItemPromoAmounts();
    }
}
