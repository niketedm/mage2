<?php
namespace Rootways\Authorizecim\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 */
class VisaCheckoutDataAssignObserver extends AbstractDataAssignObserver
{
	const VISA_DATA_VAL = 'encPaymentData';
    const VISA_DATA_KEY = 'encKey';
    const VISA_CALL_ID = 'callid';
    
    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::VISA_DATA_VAL,
        self::VISA_DATA_KEY,
        self::VISA_CALL_ID
    ];
	
	/**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
