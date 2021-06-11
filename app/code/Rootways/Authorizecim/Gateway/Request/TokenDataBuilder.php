<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class TokenDataBuilder implements BuilderInterface
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
        $transactionType = $this->customHelper->getPaymentAction();
        $extensionAttributes = $payment->getExtensionAttributes();
        $paymentToken = $extensionAttributes->getVaultPaymentToken();
        $custProId = $this->customHelper->formatedCustomerId($paymentToken->getGatewayToken());
        $paymentId = $this->customHelper->getPaymentIdByToken($paymentToken);
        $result['transactionRequest'] = [
            'transactionType' => $transactionType,
            'amount'   => $amount,
            'profile' => [
                'customerProfileId' => $custProId,
                'paymentProfile' => [
                    'paymentProfileId' => $paymentId
                ]
            ]
        ];

        return $result;
    }
}
