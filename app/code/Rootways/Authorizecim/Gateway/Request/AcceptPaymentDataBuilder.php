<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class AcceptPaymentDataBuilder implements BuilderInterface
{
    public function build(array $buildSubject)
    {  
        $paymentDO = SubjectReader::readPayment($buildSubject);
        
        $payment = $paymentDO->getPayment();
        $result['heldTransactionRequest'] = [
            'action' => 'approve',
            'refTransId' => $payment->getLastTransId()
        ];
        
        return $result;
    }
}
