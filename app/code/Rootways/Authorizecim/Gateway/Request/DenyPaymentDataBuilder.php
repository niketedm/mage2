<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class DenyPaymentDataBuilder implements BuilderInterface
{
    public function build(array $buildSubject)
    {  
        $paymentDO = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $result['heldTransactionRequest'] = [
            'action' => 'decline',
            'refTransId' => $payment->getLastTransId()
        ];
        
        return $result;
    }
}
