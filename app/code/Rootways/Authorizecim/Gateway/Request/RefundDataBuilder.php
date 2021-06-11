<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class RefundDataBuilder implements BuilderInterface
{
    public function build(array $buildSubject)
    {  
        $paymentDO = SubjectReader::readPayment($buildSubject);
        
        $payment = $paymentDO->getPayment();
        $amount = SubjectReader::readAmount($buildSubject);
        $chargeId = str_replace('-refund', '', $payment->getTransactionId());
        $ccNumber = '';
        if ($payment->getCcLast4() != '') {
            $ccNumber = substr($payment->getCcLast4(), -4);
        }
        $order = $paymentDO->getOrder();
        $orderID = $order->getOrderIncrementId();
        $result['transactionRequest'] = [
            'transactionType' => 'refundTransaction',
            'amount' => $amount,
            'payment' => [
                'creditCard' => [
                    'cardNumber' => $ccNumber,
                    'expirationDate' => 'XXXX'
                ]
            ],
            'refTransId' => $chargeId,
            'order' => [
                'invoiceNumber' => $orderID
            ]
        ];
        return $result;
    }
}
