<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class  AuthorizationResponseValidator
 */
abstract class AbstractAuthorizationResponseValidator extends AbstractStatusInquiryResponseValidator
{
    const AUTHORIZATION_APPROVED_STATUS_CODES = ['000'];
    const AUTHORIZATION_DECLINED_STATUS_CODE = '001';
    const PROMO_CODE_VALIDATION_FAILED_STATUS_CODE = '402';
    const ADDRESS_VERIFICATION_FAILED_STATUS_CODE = '403';

    const STATUS_AUTHORIZATION_DECLINED = 'declined';
    const STATUS_PROMO_CODE_VALIDATION_FAILED = 'promo_validation_failed';
    const STATUS_ADDRESS_VERIFICATION_FAILED = 'address_verification_failed';

    /**
     * Check if status code is successful
     *
     * @return bool
     */
    protected function isAuthorizationSuccessful()
    {
        return in_array($this->reader->getStatusCode(), self::AUTHORIZATION_APPROVED_STATUS_CODES, true);
    }

    /**
     * Check if status code indicates that authorization has been declined
     *
     * @return bool
     */
    protected function isAuthorizationDeclined()
    {
        return $this->reader->getStatusCode() === self::AUTHORIZATION_DECLINED_STATUS_CODE;
    }
}
