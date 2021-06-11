<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Rootways\Authorizecim\Helper\Data;

/**
 * Class AddressDataBuilder
 */
class AddressDataBuilder implements BuilderInterface
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
        $strAdd = $billingAddress->getStreetLine1() .' '.$billingAddress->getStreetLine2();
        $result['transactionRequest'] = [
            'customer' => [
                'email' => $billingAddress->getEmail()
            ],
            'billTo' => [
                'firstName' => $this->customHelper->subStrFun($billingAddress->getFirstname(), 49),
                'lastName' =>  $this->customHelper->subStrFun($billingAddress->getLastname(), 49),
                'company' =>  $this->customHelper->subStrFun($billingAddress->getCompany(), 49),
                'address' => $this->customHelper->subStrFun($strAdd, 49),
                'city' =>  $this->customHelper->subStrFun($billingAddress->getCity(), 39),
                'state' =>  $this->customHelper->subStrFun($billingAddress->getRegionCode(), 39),
                'zip' =>  $this->customHelper->subStrFun($billingAddress->getPostcode(), 19),
                'country' => $billingAddress->getCountryId(),
                'phoneNumber' =>  $this->customHelper->subStrFun($billingAddress->getTelephone(), 28)
            ]
        ];
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $shipStrAdd = $shippingAddress->getStreetLine1() .' '.$shippingAddress->getStreetLine2();
            $result['transactionRequest']['shipTo'] = [
                'firstName' => $this->customHelper->subStrFun($shippingAddress->getFirstname(), 49),
                'lastName' =>  $this->customHelper->subStrFun($shippingAddress->getLastname(), 49),
                'company' =>  $this->customHelper->subStrFun($shippingAddress->getCompany(), 49),
                'address' => $this->customHelper->subStrFun($shipStrAdd, 49),
                'city' =>  $this->customHelper->subStrFun($shippingAddress->getCity(), 39),
                'state' =>  $this->customHelper->subStrFun($shippingAddress->getRegionCode(), 39),
                'zip' =>  $this->customHelper->subStrFun($shippingAddress->getPostcode(), 19),
                'country' => $shippingAddress->getCountryId()
            ];
        }
        $result['transactionRequest']['customerIP'] = $this->customHelper->getCustomerIp();

        return $result;
    }
}
