<?php

namespace Synchrony\DigitalBuy\Gateway\Request;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Synchrony\DigitalBuy\Gateway\Config\CommonConfig as Config;

/**
 * Class BuyServiceConfigDataBuilder
 */
class BuyServiceConfigDataBuilder implements BuilderInterface
{
    const CLIENT_KEY  = 'Client';
    const PARTNER_CODE_KEY = 'PartnerCode';

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
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $order = SubjectReader::readPayment($buildSubject)->getOrder();
        $storeId = $order->getStoreId();

        $result = [];
        $clientValue = $this->config->getBuyServiceClient($storeId);
        if ($clientValue) {
            $result[self::CLIENT_KEY] = $clientValue;
        }
        $partnerCode = $this->config->getBuyServicePartnerCode($storeId);
        if ($partnerCode) {
            $result[self::PARTNER_CODE_KEY] = $partnerCode;
        }

        return $result;
    }
}
