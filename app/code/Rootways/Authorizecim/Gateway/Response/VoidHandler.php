<?php
namespace Rootways\Authorizecim\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Payment Details Handler
 */
class VoidHandler implements HandlerInterface
{
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();
        //$payment->setTransactionId(null);
        $payment->setIsTransactionClosed(true);
        $payment->setShouldCloseParentTransaction(true);
    }
}
