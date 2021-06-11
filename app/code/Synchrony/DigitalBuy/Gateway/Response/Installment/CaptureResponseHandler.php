<?php

namespace Synchrony\DigitalBuy\Gateway\Response\Installment;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction\AdditionalInfoManager as TransAdditionalInfoManager;
use Synchrony\DigitalBuy\Gateway\Response\StatusInquiryReader;

/**
 * Installment Capture response Handler
 */
class CaptureResponseHandler implements HandlerInterface
{
    /**
     * @var CaptureResponseReader
     */
    private $reader;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @var TransAdditionalInfoManager
     */
    private $transAdditionalInfoManager;

    /**
     * @var StatusInquiryReader
     */
    private $statusInquiryReader;

    /**
     * @param CaptureResponseReader $reader
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     * @param TransAdditionalInfoManager $transAdditionalInfoManager
     * @param StatusInquiryReader
     */
    public function __construct(
        CaptureResponseReader $reader,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager,
        TransAdditionalInfoManager $transAdditionalInfoManager,
        StatusInquiryReader $statusInquiryReader
    ) {
        $this->reader = $reader;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        $this->transAdditionalInfoManager = $transAdditionalInfoManager;
        $this->statusInquiryReader = $statusInquiryReader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $this->reader->setData($response);

        $paymentDO = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();
        $transactionId = $this->reader->getTransactionId();
        $payment->setTransactionId($transactionId);
        $authorizeTransaction = $payment->getAuthorizationTransaction();
        $authorizeTransactionReader = $this->statusInquiryReader->setData(
            $this->transAdditionalInfoManager->setTransaction($authorizeTransaction)->getRawDetails()
        );
        $this->paymentAdditionalInfoManager->setPayment($payment)
            ->addPaidPromoAmount(
                $authorizeTransactionReader->getPromoCode(),
                SubjectReader::readAmount($handlingSubject)
            )
            ->setTransactionRawDetails($response);
        $payment->setIsTransactionClosed(false);

        $order->addStatusHistoryComment(__(
            'Executed Synchrony Capture for promo code %1, amount: %2. Transaction ID: "%3"',
            $authorizeTransactionReader->getPromoCode(),
            $order->getBaseCurrency()->formatTxt(SubjectReader::readAmount($handlingSubject)),
            $transactionId
        ));
    }
}
