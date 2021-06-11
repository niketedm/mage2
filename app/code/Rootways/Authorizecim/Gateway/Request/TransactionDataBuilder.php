<?php
namespace Rootways\Authorizecim\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Rootways\Authorizecim\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AddressDataBuilder
 */
class TransactionDataBuilder implements BuilderInterface
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
        
        $dataDescriptor = $payment->getAdditionalInformation(DataAssignObserver::ACCEPTJS_DATA_DESCRIPTOR);
        if ($dataDescriptor != '' && $this->customHelper->enableAcceptjs()) {
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
        } else {
            if ($this->customHelper->getEnableCaptcha() != 0 &&
                $this->customHelper->checkAdmin() != 'adminhtml'
               ) {
                $CAPTCHA = $payment->getAdditionalInformation(DataAssignObserver::G_CAPTCHA);
                $verifyC = $this->customHelper->verifyGC($CAPTCHA);
                if ($verifyC != '') {
                    throw new LocalizedException(__($verifyC));
                }
            }
            $accountNum = $payment->getAdditionalInformation(DataAssignObserver::CC_NUMBER);
            $dt = \DateTime::createFromFormat('Y', $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_YEAR));
            $expYear = trim($payment->getAdditionalInformation(DataAssignObserver::CC_EXP_YEAR));
            if ($dt) {
                $expYear = $dt->format('Y');
            }
            $expMonth = sprintf("%02d", $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_MONTH));
            $expDate = $expYear.'-'.$expMonth;
            $cvv = $payment->getAdditionalInformation(DataAssignObserver::CC_CID);
            
            $result['transactionRequest'] = [
                'transactionType' => $transactionType,
                'amount'   => $amount,
                'payment' => [
                    'creditCard' => [
                        'cardNumber' => $accountNum,
                        'expirationDate' => $expDate,
                        'cardCode' => $cvv
                    ]
                ]
            ];
        }
        return $result;
    }
}
