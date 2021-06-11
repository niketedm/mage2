<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Rootways\Authorizecim\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Vault Data Builder
 */
class VaultDataBuilder implements BuilderInterface
{
    /**
     *
     * @var \Rootways\Authorizecim\Helper\Data
     */
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
        
        $result = array();
        $saveCard = $payment->getAdditionalInformation(DataAssignObserver::SAVE_CARD);
        
        $cardcollection = $this->customHelper->getSavedCreditCard();
        $createProfile = 'false';
        if (count($cardcollection) < 0 || $cardcollection->getFirstItem()->getGatewayToken() == '') {
            $createProfile = 'true';
        }
        
        if ($saveCard == '1') {
            $result['transactionRequest'] = [
                'profile' => [
                    'createProfile' => $createProfile
                ]
            ];
        }
        
        return $result;
    }
}
