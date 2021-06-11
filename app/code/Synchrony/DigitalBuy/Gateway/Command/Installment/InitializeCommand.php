<?php

namespace Synchrony\DigitalBuy\Gateway\Command\Installment;

use Magento\Payment\Gateway\CommandInterface;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class InitializeCommand
 * @package Synchrony\DigitalBuy\Gateway\Command/Installment
 */
class InitializeCommand implements CommandInterface
{
    /**
     * @param array $commandSubject
     */
    public function execute(array $commandSubject)
    {
        $stateObject = SubjectReader::readStateObject($commandSubject);
        $paymentDataObject = SubjectReader::readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDataObject->getPayment();
        if (!$payment instanceof Payment) {
            throw new \LogicException('Order Payment should be provided');
        }

        $payment->getOrder()->setCanSendNewEmailFlag(false);

        $stateObject->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData('is_notified', false);
    }
}
