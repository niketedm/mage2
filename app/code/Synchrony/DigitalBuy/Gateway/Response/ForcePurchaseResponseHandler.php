<?php

namespace Synchrony\DigitalBuy\Gateway\Response;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Synchrony\DigitalBuy\Gateway\Response\BuyServiceResponseReader;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

/**
 * Force purchase response Handler
 */
class ForcePurchaseResponseHandler implements HandlerInterface
{
    /**
     * @var BuyServiceResponseReader
     */
    private $reader;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @param BuyServiceResponseReader $reader
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     */
    public function __construct(
        BuyServiceResponseReader $reader,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager
    ) {
        $this->reader = $reader;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
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
        $promoCode = SubjectReader::readPromoCode($handlingSubject);
        $transactionId = $this->reader->getTransactionId();
        $parentTransactionIdPart = $promoCode . '-' . $transactionId;
        $paymentTransactionId = $payment->getTransactionId()
            ? $payment->getTransactionId() . '_' . $parentTransactionIdPart : $parentTransactionIdPart;
        $payment->setTransactionId($paymentTransactionId);

        $this->paymentAdditionalInfoManager->setPayment($payment)
            ->addPaidPromoAmount($promoCode, SubjectReader::readAmount($handlingSubject))
            ->addTransactionSubTransactionData($promoCode, $response);

        $order->addStatusHistoryComment(__(
            'Executed Synchrony Force Purchase for promo code %1, amount: %2. Transaction ID: "%3"',
            $promoCode,
            $order->getBaseCurrency()->formatTxt(SubjectReader::readAmount($handlingSubject)),
            $transactionId
        ));
    }
}
