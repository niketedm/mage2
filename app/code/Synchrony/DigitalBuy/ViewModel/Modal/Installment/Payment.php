<?php

namespace Synchrony\DigitalBuy\ViewModel\Modal\Installment;

use Magento\Directory\Model\RegionFactory;
use Synchrony\DigitalBuy\Model\Session as SynchronySession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Synchrony\DigitalBuy\Gateway\Config\InstallmentConfig as SynchronyConfig;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Registry;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

class Payment extends \Synchrony\DigitalBuy\ViewModel\Modal\AbstractPayment
{
    /**
     * Registry key to store token
     */
    const TOKEN_REGISTRY_KEY = 'synchrony_installment_token';

    /**
     * Retrieve Installment Product Code Group Code (PCGC)
     *
     * @return string
     */
    public function getPcgc()
    {
        return $this->synchronyConfig->getPcgc($this->getStoreId());
    }

    /**
     * Retrieve order grand total
     *
     * @return string
     */
    public function getOrderGrandTotal()
    {
        return number_format($this->getCurrentOrder()->getBaseGrandTotal(), 2, '.', '');
    }
}
