<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class CaptureTransactionDataBuilder implements BuilderInterface
{
    public function build(array $buildSubject)
    {  
        $paymentDO = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $amount = SubjectReader::readAmount($buildSubject);
        $chargeId = str_replace('-capture', '', $payment->getTransactionId());
        
        $result['transactionRequest'] = [
            'transactionType' => 'priorAuthCaptureTransaction',
            'amount' => $amount,
            'refTransId' => $chargeId
        ];
        return $result;
    }
}
