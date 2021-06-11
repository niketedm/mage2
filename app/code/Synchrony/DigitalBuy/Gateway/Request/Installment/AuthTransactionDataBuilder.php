<?php

namespace Synchrony\DigitalBuy\Gateway\Request\Installment;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Synchrony\DigitalBuy\Gateway\Response\StatusInquiryReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction\AdditionalInfoManager as TransAdditionalInfoManager;

/**
 * Class Auth Data Builder
 */
class AuthTransactionDataBuilder implements BuilderInterface
{
    const ACCOUNT_TOKEN_KEY = 'token';
    const SETTLEMENT_ID_KEY = 'settlementId';

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
     * Build Capture Request Data from Authorization Transaction
     *
     * @param array $buildSubject
     * @return array
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
            self::ACCOUNT_TOKEN_KEY => $authorizeTransactionReader->getAccountToken(),
            self::SETTLEMENT_ID_KEY => $authorizeTransactionReader->getSettlementId()
        ];
    }
}
