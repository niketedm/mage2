<?php

namespace Synchrony\DigitalBuy\Model\CheckoutConfigProvider;

/**
 * Class CheckoutConfigProvider
 * @package Synchrony\DigitalBuy\Model
 */
class InstallmentProvider extends AbstractProvider
{
    /**
     * Retrieve payment completion modal page URL
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return $this->urlBuilder->getUrl('digitalbuy/installment/modal_payment', ['_secure' => true]);
    }
}
