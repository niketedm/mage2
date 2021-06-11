<?php

namespace Synchrony\DigitalBuy\Gateway\Request;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

/**
 * Class DigitalBuyTokenDataBuilder
 */
class DigitalBuyTokenDataBuilder implements BuilderInterface
{
    /**
     * Token Key
     */
    const TOKEN_ID = 'tokenId';

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    public function __construct(PaymentAdditionalInfoManager $paymentAdditionalInfoManager)
    {
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $result = [];
        $tokenId = null;
        try {
            $payment = SubjectReader::readPayment($buildSubject)->getPayment();
            $tokenId = $this->paymentAdditionalInfoManager->setPayment($payment)->getDigitalBuyToken();
        } catch (\Exception $e) {}

        // fallback to subject
        if ($tokenId === null) {
            $tokenId = SubjectReader::readToken($buildSubject);
        }
        $result[self::TOKEN_ID] = $tokenId;
        return $result;
    }
}
