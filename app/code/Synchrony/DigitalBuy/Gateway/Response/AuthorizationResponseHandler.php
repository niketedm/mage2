<?php

namespace Synchrony\DigitalBuy\Gateway\Response;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Synchrony\DigitalBuy\Gateway\Response\StatusInquiryReader;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

/**
 * Payment Details Handler
 */
class AuthorizationResponseHandler implements HandlerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var StatusInquiryReader
     */
    private $reader;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param StatusInquiryReader $reader
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        StatusInquiryReader $reader,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager
    ) {
        $this->orderRepository = $orderRepository;
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
        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();
        $orderPayment->setTransactionId($this->reader->getTransactionId());
        $this->paymentAdditionalInfoManager->setPayment($orderPayment)->setTransactionRawDetails($response);
        $orderPayment->setIsTransactionClosed(false);
    }
}
