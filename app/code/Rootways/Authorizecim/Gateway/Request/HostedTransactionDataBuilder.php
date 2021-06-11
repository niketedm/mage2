<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Rootways\Authorizecim\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class AddressDataBuilder
 */
class HostedTransactionDataBuilder implements BuilderInterface
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
        $amount = SubjectReader::readAmount($buildSubject);
        $transactionType = $this->customHelper->getHostedPaymentAction();
        
        $dataDescriptor = $payment->getAdditionalInformation(DataAssignObserver::ACCEPTJS_DATA_DESCRIPTOR);
        $opaqueData = $payment->getAdditionalInformation(DataAssignObserver::ACCEPTJS_DATA_VALUE);

        $result['transactionRequest'] = [
            'transactionType' => $transactionType,
            'amount'   => $amount,
            'payment' => [
                'opaqueData' => [
                    'dataDescriptor' => $dataDescriptor,
                    'dataValue' => $opaqueData
                ]
            ]
        ];
        return $result;
    }
}
