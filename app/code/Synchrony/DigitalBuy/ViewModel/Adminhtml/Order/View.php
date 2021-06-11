<?php

namespace Synchrony\DigitalBuy\ViewModel\Adminhtml\Order;

use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

class View implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param PaymentAdditionalInfoManager
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager
    ) {
        $this->registry = $registry;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
    }

    /**
     * Retrieve current order from registry
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Retrive payment
     *
     * @return \Magento\Sales\Model\Order\Payment
     */
    public function getPayment()
    {
        return $this->getOrder()->getPayment();
    }

    /**
     * Retrieve synchrony promotions information from payment
     *
     * @return bool|array
     */
    public function getPromoAmounts()
    {
        $payment = $this->getPayment();
        if (!$payment) {
            return false;
        }

        return $this->paymentAdditionalInfoManager->setPayment($payment)->getPromoAmounts();
    }

    /**
     * Retrieve default promo code applied
     *
     * @return array|bool
     */
    public function getDefaultPromoCode()
    {
        $payment = $this->getPayment();
        if (!$payment) {
            return false;
        }

        return $this->paymentAdditionalInfoManager->setPayment($payment)->getDefaultPromoCode();
    }

    /**
     * Retrieve rule metadata
     *
     * @return array|bool
     */
    public function getRuleMetadata()
    {
        $payment = $this->getPayment();
        if (!$payment) {
            return false;
        }

        return $this->paymentAdditionalInfoManager->setPayment($payment)->getRuleMetadata();
    }
}
