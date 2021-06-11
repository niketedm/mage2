<?php

namespace Synchrony\DigitalBuy\Gateway\Request;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Synchrony\DigitalBuy\Gateway\Response\StatusInquiryReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction\AdditionalInfoManager as TransAdditionalInfoManager;

/**
 * Class ForcePurchaseDataBuilder
 */
class ForcePurchaseDataBuilder implements BuilderInterface
{
    const AUTH_CODE_KEY = 'AuthCode';
    const FORCE_PUR_AMOUNT_KEY = 'ForcePurAmount';

    /**
     * @var TransAdditionalInfoManager
     */
    private $transAdditionalInfoManager;

    /**
     * @var StatusInquiryReader
     */
    private $statusInquiryReader;

    /**
     * ForcePurchaseDataBuilder constructor.
     * @param TransAdditionalInfoManager $transAdditionalInfoManager
     * @param StatusInquiryReader $statusInquiryReader
     */
    public function __construct(
        TransAdditionalInfoManager $transAdditionalInfoManager,
        StatusInquiryReader $statusInquiryReader
    ) {
        $this->transAdditionalInfoManager = $transAdditionalInfoManager;
        $this->statusInquiryReader = $statusInquiryReader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();
        $authorizeTransaction = $payment->getAuthorizationTransaction();
        $authorizeTransactionReader = $this->statusInquiryReader->setData(
            $this->transAdditionalInfoManager->setTransaction($authorizeTransaction)->getRawDetails()
        );

        return [
            self::AUTH_CODE_KEY => $authorizeTransactionReader->getAuthCode(),
            self::FORCE_PUR_AMOUNT_KEY => SubjectReader::readAmount($buildSubject)
        ];
    }
}
