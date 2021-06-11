<?php

namespace Synchrony\DigitalBuy\ViewModel\Modal\Revolving;

use Magento\Directory\Model\RegionFactory;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as SynchronyConfig;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Registry;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

class Payment extends \Synchrony\DigitalBuy\ViewModel\Modal\AbstractPayment
{
    /**
     * Retrieve Synchrony promo codes applied to order
     *
     * @return array
     */
    public function getPromoAmounts()
    {
        $order = $this->getCurrentOrder();
        $promotionalData = $this->paymentAdditionalInfoManager->setPayment($order->getPayment())
            ->getPromoAmounts();

        return $promotionalData ?: [];
    }

    /**
     * Retrieve Synchrony default promo code applied to order
     *
     * @return string
     */
    public function getDefaultPromoCode()
    {
        $order = $this->getCurrentOrder();

        return $this->paymentAdditionalInfoManager->setPayment($order->getPayment())
            ->getDefaultPromoCode();
    }
}
