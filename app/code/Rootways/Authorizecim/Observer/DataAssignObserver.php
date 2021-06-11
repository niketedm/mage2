<?php
/**
 * Authorize.net Payment Module.
 *
 * @category  Payment Integration
 * @package   Rootways_Authorizecim
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2021 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
 */
namespace Rootways\Authorizecim\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
	const CC_NUMBER = 'cc_number';
    const CC_CID = 'cc_cid';
	const CC_TYPE = 'cc_type';
	const CC_EXP_MONTH = 'cc_exp_month';
	const CC_EXP_YEAR = 'cc_exp_year';
	const SAVE_CARD = 'is_active_payment_token_enabler';
    const ACCEPTJS_DATA_VALUE = 'data_value';
    const ACCEPTJS_DATA_DESCRIPTOR = 'data_descriptor';
    const G_CAPTCHA = 'captcha_string';
    
    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::CC_NUMBER,
        self::CC_CID,
		self::CC_TYPE,
        self::CC_EXP_MONTH,
        self::CC_EXP_YEAR,
		self::SAVE_CARD,
        self::ACCEPTJS_DATA_VALUE,
        self::ACCEPTJS_DATA_DESCRIPTOR,
        self::G_CAPTCHA
    ];
	
	/**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);
        
        $data_1 = $observer->getData('data');

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
            } else {
                $paymentInfo->unsAdditionalInformation($additionalInformationKey);
            }
        }
    }
}
