<?php

namespace Synchrony\DigitalBuy\Gateway\Request\Installment;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Synchrony\DigitalBuy\Gateway\Config\AbstractPaymentConfig as Config;

/**
 * Class MerchantNumberDataBuilder
 */
class MerchantNumberDataBuilder implements BuilderInterface
{
    const MERCHANT_NUMBER_KEY = 'merchantNumber';

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Build Capture data request
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $order = SubjectReader::readPayment($buildSubject)->getOrder();
        $storeId = $order->getStoreId();
        return [
            self::MERCHANT_NUMBER_KEY => $this->config->getDigitalBuyApiMerchantId($storeId)
        ];
    }
}
