<?php

namespace Synchrony\DigitalBuy\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

/**
 * Class CaptureStratergyCommand
 */
class CaptureStratergyCommand implements CommandInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderSender $orderSender
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderSender $orderSender,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderSender = $orderSender;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $commandSubject)
    {
        $paymentDataObject = SubjectReader::readPayment($commandSubject);
        $payment = $paymentDataObject->getPayment();
        $order = $payment->getOrder();
        $baseTotalDue = $order->getBaseTotalDue();
        $totalDue = $order->getTotalDue();
        switch ($payment->getMethodInstance()->getConfigPaymentAction()) {
            case AbstractMethod::ACTION_AUTHORIZE_CAPTURE:
                $payment->authorize(true, $baseTotalDue);
                $payment->setAmountAuthorized($totalDue);
                $this->orderRepository->save($order);
                $payment->setTransactionId(null);
                $payment->capture();
                $this->orderRepository->save($order);
                break;
            case AbstractMethod::ACTION_AUTHORIZE:
                $payment->authorize(true, $baseTotalDue);
                $payment->setAmountAuthorized($totalDue);
                $this->orderRepository->save($order);
                break;
        }

        if (!$order->getEmailSent()) {
            try {
                $this->orderSender->send($order);
            } catch (\Exception $e) {
                $this->logger->critical("Error while sending Order email:" . $e->getMessage());
            }
        }
    }
}
