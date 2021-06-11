<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Rootways\Authorizecim\Helper\Data;

/**
 * Class AddressDataBuilder
 */
class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @var Data
     */
    private $helper;
    
    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    )
    {
        $this->customHelper = $helper;
    }
    
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();
        
        if ($this->customHelper->getCustomerId() != '') {
            $result['transactionRequest']['customer']['id'] = $this->customHelper->getCustomerId();
        }
        
        $result['transactionRequest']['customer']['email'] =  $billingAddress->getEmail();
        
        return $result;
    }
}
