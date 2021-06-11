<?php

namespace Synchrony\DigitalBuy\Model\Payment\Method;

use Magento\Quote\Api\Data\CartInterface;

class AbstractAdapter extends \Magento\Payment\Model\Method\Adapter
{
    /**
     * @var string
     */
    private $lastCartValidationMsg;

    /**
     * Check if the cart contents are not eligible for payment method
     *
     * @param CartInterface $quote
     * @return bool
     */
    public function canUseForCart(CartInterface $quote)
    {
        $this->lastCartValidationMsg = null;
        try {
            $cartValidator = $this->getValidatorPool()->get('cart');
        } catch (\Exception $e) {
            return true;
        }

        $result = $cartValidator->validate(['payment' => $this, 'quote' => $quote]);
        $this->lastCartValidationMsg = implode(',', $result->getFailsDescription());
        return $result->isValid();
    }

    /**
     * Get cart validation message
     *
     * @return string
     */
    public function getLastCartValidationMsg()
    {
        return $this->lastCartValidationMsg;
    }
}
