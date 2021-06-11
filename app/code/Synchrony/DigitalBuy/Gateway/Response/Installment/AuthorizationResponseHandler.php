<?php

namespace Synchrony\DigitalBuy\Gateway\Response\Installment;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Synchrony\DigitalBuy\Gateway\Response\StatusInquiryReader;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;
use Synchrony\DigitalBuy\Model\Installment\PromoCodeApplier;

/**
 * Payment Details Handler
 */
class AuthorizationResponseHandler implements HandlerInterface
{
    /**
     * @var StatusInquiryReader
     */
    private $reader;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @var PromoCodeApplier
     */
    private $promoCodeApplier;

    /**
     * @param StatusInquiryReader $reader
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     * @param PromoCodeApplier
     */
    public function __construct(
        StatusInquiryReader $reader,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager,
        PromoCodeApplier $promoCodeApplier
    ) {
        $this->reader = $reader;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        $this->promoCodeApplier = $promoCodeApplier;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $this->reader->setData($response);

        $paymentDO = SubjectReader::readPayment($handlingSubject);
        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();
        $orderPayment->setTransactionId($this->reader->getTransactionId());
        $this->paymentAdditionalInfoManager->setPayment($orderPayment)->setTransactionRawDetails($response);
        $this->promoCodeApplier->applyPromoCode($orderPayment->getOrder(), $this->reader->getPromoCode());
        $orderPayment->setIsTransactionClosed(false);
    }
}
