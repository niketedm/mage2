<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class CommonDataBuilder implements BuilderInterface
{
    private $helper;
    
    public function __construct(
        \Rootways\Authorizecim\Helper\Data $helper
    )
    {
        $this->customHelper = $helper;
    }
    
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $orderStoreId = $order->getStoreId();
        
        $result['merchantAuthentication'] = [
            'name' => $this->customHelper->getApiLoginId($orderStoreId),
            'transactionKey'   => $this->customHelper->getTransactionKey($orderStoreId)
        ];
        
        return $result;
    }
}
