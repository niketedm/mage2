<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Rootways\Authorizecim\Helper\Data;

/**
 * Class AddressDataBuilder
 */
class OrderDataBuilder implements BuilderInterface
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
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();
        
        $result['transactionRequest'] = [
            'order' => [
                'invoiceNumber' => $paymentDO->getOrder()->getOrderIncrementId()
            ]
        ];
        
        if ($this->customHelper->sendCartItem() == 1) {
            $itemCollection = array();
            foreach ($order->getItems() as $item) {
                $taxable = 0;
                if (!empty($item->getTaxAmount())) {
                    $taxable = 1;
                }
                $itemCollection['lineItem'][] = [
                    "itemId" => $item->getSku(),
                    "name" => trim(substr($item->getName(), 0, 31)),
                    "description" => trim(substr($item->getName(), 0, 255 )),
                    "quantity" => $item->getQtyOrdered(),
                    "unitPrice" => $item->getPrice(),
                    "taxable" => $taxable
                ];
            }
            $result['transactionRequest']['lineItems'] = $itemCollection;
        }
        
        if (!empty($order->getBaseTaxAmount())) {
            $result['transactionRequest']['tax'] = [
                'amount' => $order->getBaseTaxAmount()
            ];
        }
        
        if (!empty($order->getBaseShippingAmount())) {
            $result['transactionRequest']['shipping'] = [
                'amount' => $order->getBaseShippingAmount(),
                'name' => $order->getShippingMethod(),
                'description' => $order->getShippingDescription()
            ];
        }
        
        return $result;
    }
}
