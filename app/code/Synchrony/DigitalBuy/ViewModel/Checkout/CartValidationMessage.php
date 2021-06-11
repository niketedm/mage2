<?php

namespace Synchrony\DigitalBuy\ViewModel\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Synchrony\DigitalBuy\Model\Payment\Method\AbstractAdapter;
use Magento\Payment\Model\Checks\SpecificationFactory;
use Magento\Payment\Model\Method\AbstractMethod;

class CartValidationMessage implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var SpecificationFactory
     */
    private $specificationFactory;

    /**
     * @var AbstractAdapter[]
     */
    private $paymentInstances = [];

    public function __construct(
        CheckoutSession $checkoutSession,
        SpecificationFactory $specificationFactory,
        $paymentInstances = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->specificationFactory = $specificationFactory;
        $this->paymentInstances = $paymentInstances;
    }

    /**
     * Check should we render the block.
     * @return bool
     */
    public function canRenderBlock()
    {
        foreach ($this->paymentInstances as $methodInstance) {
            if ($this->isMethodAvailable($methodInstance)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isMethodAvailable($methodInstance)
    {
        $quote = $this->checkoutSession->getQuote();

        //Validated with quote currency && other validation associated with payment method.
        if ($methodInstance->isAvailable($quote) && $this->canUseMethod($quote, $methodInstance)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve cart validation message for all payments
     *
     * @return array
     */
    public function getCartValidationMessages()
    {
        $messages = [];
        $quote = $this->checkoutSession->getQuote();

        foreach ($this->paymentInstances as $methodInstance) {
            if ($this->isMethodAvailable($methodInstance) && !$methodInstance->canUseForCart($quote)) {
                $messages[] = $methodInstance->getLastCartValidationMsg();
            }
        }

        return $messages;
    }

    /**
     * @param $methodInstance
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function canUseMethod($quote, $methodInstance)
    {
        return $this->specificationFactory->create(
            [
                AbstractMethod::CHECK_USE_CHECKOUT,
                AbstractMethod::CHECK_USE_FOR_COUNTRY,
                AbstractMethod::CHECK_USE_FOR_CURRENCY,
                AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX
            ]
        )->isApplicable(
            $methodInstance,
            $quote
        );
    }
}
