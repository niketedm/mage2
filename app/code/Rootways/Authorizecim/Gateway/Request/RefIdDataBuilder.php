<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class RefIdDataBuilder implements BuilderInterface
{
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        
        $order = $paymentDO->getOrder();
        $result = [
            'refId'=> $order->getOrderIncrementId()
        ];
        
        return $result;
    }
}
