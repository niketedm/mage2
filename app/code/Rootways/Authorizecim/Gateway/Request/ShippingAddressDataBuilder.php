<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class AddressDataBuilder
 */
class ShippingAddressDataBuilder implements BuilderInterface
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
        
        $shippingAddress = $order->getShippingAddress();
        $result = array();
        if ($shippingAddress && $this->customHelper->sendShipping() == '1') {
            $strAdd = $shippingAddress->getStreetLine1() .' '.$shippingAddress->getStreetLine2();
            $result['transactionRequest'] = [
                'shipTo' => [
                    'firstName' => $this->customHelper->subStrFun($shippingAddress->getFirstname(), 49),
                    'lastName' =>  $this->customHelper->subStrFun($shippingAddress->getLastname(), 49),
                    'company' =>  $this->customHelper->subStrFun($shippingAddress->getCompany(), 49),
                    'address' => $this->customHelper->subStrFun($strAdd, 49),
                    'city' => $this->customHelper->subStrFun($shippingAddress->getCity(), 39),
                    'state' => $this->customHelper->subStrFun($shippingAddress->getRegionCode(), 39),
                    'zip' => $this->customHelper->subStrFun($shippingAddress->getPostcode(), 19),
                    'country' => $shippingAddress->getCountryId()
                ]
            ];
        }
        return $result;
    }
}
