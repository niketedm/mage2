<?php

namespace Synchrony\DigitalBuy\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class InstallmentConfig extends AbstractPaymentConfig
{
    /**
     * Synchrony DigitalBuy Installment payment method code.
     *
     * @var string
     */
    const METHOD_CODE = 'synchrony_digitalbuy_installment';

    /**
     * Retrieve Merchant ID to be used in Digital Buy API calls
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getDigitalBuyApiMerchantId($storeId = null)
    {
        return $this->commonConfig->getInstallmentApiMerchantId($storeId);
    }

    /**
     * Retrieve Digital Buy API password
     *
     * @param string|int|null $storeId
     * @return mixed
     */
    public function getDigitalBuyApiPassword($storeId = null)
    {
        return $this->commonConfig->getInstallmentApiPassword($storeId);
    }

    /**
     * Retrieve Product Code Group Code
     *
     * @param string|int|null $storeId
     * @return string
     */
    public function getPCGC($storeId = null)
    {
        return $this->getValue('pcgc', $storeId);
    }
}
