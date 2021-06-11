<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class AddressDataBuilder
 */
class BillingAddressDataBuilder implements BuilderInterface
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
        $order = $paymentDO->getOrder();
        
        $billingAddress = $order->getBillingAddress();
        $result = array();
        if ($this->customHelper->sendBilling() == '1') {
            $strAdd = $billingAddress->getStreetLine1() .' '.$billingAddress->getStreetLine2();
            $result['transactionRequest'] = [
                'billTo' => [
                    'firstName' => $this->customHelper->subStrFun($billingAddress->getFirstname(), 49),
                    'lastName' =>  $this->customHelper->subStrFun($billingAddress->getLastname(), 49),
                    'company' =>  $this->customHelper->subStrFun($billingAddress->getCompany(), 49),
                    'address' => $this->customHelper->subStrFun($strAdd, 49),
                    'city' => $this->customHelper->subStrFun($billingAddress->getCity(), 39),
                    'state' => $this->customHelper->subStrFun($billingAddress->getRegionCode(), 39),
                    'zip' => $this->customHelper->subStrFun($billingAddress->getPostcode(), 19),
                    'country' => $billingAddress->getCountryId(),
                    'phoneNumber' => $this->customHelper->subStrFun($billingAddress->getTelephone(), 28)
                ]
            ];
        }
        
        return $result;
    }
}
