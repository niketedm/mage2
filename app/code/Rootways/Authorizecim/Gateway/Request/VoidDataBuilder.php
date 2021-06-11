<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class VoidDataBuilder implements BuilderInterface
{
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        
        $payment = $paymentDO->getPayment();
        $chargeId = str_replace('-void', '', $payment->getTransactionId());
        
        $result['transactionRequest'] = [
            'transactionType' => 'voidTransaction',
            'refTransId' => $chargeId
        ];
        return $result;
    }
}
