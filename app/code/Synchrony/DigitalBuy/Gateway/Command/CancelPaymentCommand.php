<?php

namespace Synchrony\DigitalBuy\Gateway\Command;

use Magento\Checkout\Model\Session;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class CancelCommand
 */
class CancelPaymentCommand implements CommandInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var OrderManagementInterface
     */
    private $orderManagementInterface;

    /**
     * @param OrderManagementInterface $orderManagementInterface
     * @param Session $checkoutSession
     */
    public function __construct(
        OrderManagementInterface $orderManagementInterface,
        Session $checkoutSession
    ) {
        $this->orderManagementInterface = $orderManagementInterface;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Executes command basing on business object
     *
     * @param array $commandSubject
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = SubjectReader::readPayment($commandSubject);
        $payment = $paymentDO->getPayment();

        if (!$payment instanceof Payment) {
            throw new \LogicException('Order Payment should be provided');
        }

        /*Cancel Invoice if capture fails*/
        $invoiceCollection=$payment->getOrder()->getInvoiceCollection();
        foreach ($invoiceCollection as $invoice) {
            if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
                $invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_OPEN);
                $invoice->cancel();
            }
        }
        $this->orderManagementInterface->cancel($paymentDO->getOrder()->getId());

        $this->checkoutSession->setLastRealOrderId($paymentDO->getOrder()->getOrderIncrementId());
        $this->checkoutSession->restoreQuote();
    }
}
