<?php

namespace Synchrony\DigitalBuy\Gateway\Validator\Revolving;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Synchrony\DigitalBuy\Gateway\Validator\AbstractAuthorizationResponseValidator;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Address\AbstractAddress;

/**
 * Class  AuthorizationResponseValidator
 */
class AuthorizationResponseValidator extends AbstractAuthorizationResponseValidator
{

    /**
     * Validate Authorization Response
     *
     * @param array $validationSubject
     * @return \Synchrony\DigitalBuy\Gateway\Validator\Result\ValidatorResult
     */
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);
        $this->reader->setData($response);

        if (!$this->reader->hasResponseCode() || !$this->reader->getResponseCode()) {
            return $this->createResult(false, ['Response code is missing from response']);
        }

        if (!$this->isSuccessResponseCode()) {
            return $this->createResult(false, ['Response code is unsuccessful']);
        }

        if (!$this->reader->hasStatusCode() || !$this->reader->getStatusCode()) {
            return $this->createResult(false, ['Status code is missing from response']);
        }

        if ($this->isExceptionStatusCode()) {
            return $this->createResult(false, ['API returned exception']);
        }

        if ($this->isAuthorizationSuccessful()) {
            if (!$this->reader->hasAccountToken() || !$this->reader->getAccountToken()) {
                return $this->createResult(false, ['Account Token is missing from response']);
            }

            if (!$this->reader->hasTransactionAmount() || !$this->reader->getTransactionAmount()) {
                return $this->createResult(false, ['Transaction amount is missing from response']);
            }

            $amountDue = SubjectReader::readAmount($validationSubject);
            if (abs($amountDue - floatval($this->reader->getTransactionAmount())) > 0.0001) {
                return $this->createResult(false, ['Transaction amount doesn\'t match order amount']);
            }

            $order = SubjectReader::readPayment($validationSubject)->getPayment()->getOrder();
            if (!$this->validateAddress($order)) {
                return $this->createResult(
                    false,
                    ['Address verification failed'],
                    self::STATUS_ADDRESS_VERIFICATION_FAILED
                );
            }

            return $this->createResult(true);
        }

        if ($this->isUserTerminated()) {
            return $this->createResult(false, ['Terminated by user'], self::STATUS_TERMINATED);
        } elseif ($this->isTokenExpired()) {
            return $this->createResult(false, ['Token expired'], self::STATUS_TOKEN_EXPIRED);
        } elseif ($this->isApplicationPending()) {
            return $this->createResult(false, ['Application pending'], self::STATUS_APPLICATION_PENDING);
        } elseif ($this->isApplicationDeclined()) {
            return $this->createResult(false, ['Application declined'], self::STATUS_APPLICATION_DECLINED);
        } elseif ($this->isAuthorizationDeclined()) {
            return $this->createResult(false, ['Authorization declined'], self::STATUS_AUTHORIZATION_DECLINED);
        } elseif ($this->isPromoCodeValidationFailed()) {
            return $this->createResult(
                false,
                ['Promo code validation failed'],
                self::STATUS_PROMO_CODE_VALIDATION_FAILED
            );
        } elseif ($this->isAddressVerificationFailed()) {
            return $this->createResult(
                false,
                ['Address verification failed'],
                self::STATUS_ADDRESS_VERIFICATION_FAILED
            );
        }

        return $this->createResult(false, ['Unknown status code']);
    }

    /**
     * Check if status code indicates that promo codes validation failed
     *
     * @return bool
     */
    protected function isPromoCodeValidationFailed()
    {
        return $this->reader->getStatusCode() === self::PROMO_CODE_VALIDATION_FAILED_STATUS_CODE;
    }

    /**
     * Check if status code indicates that address verification failed
     *
     * @return bool
     */
    protected function isAddressVerificationFailed()
    {
        return $this->reader->getStatusCode() === self::ADDRESS_VERIFICATION_FAILED_STATUS_CODE;
    }

    /**
     * Case-insensitive strings compare with trim
     * assuming strings are in the same encoding
     *
     * @param $str1
     * @param $str2
     * @return int|\lt
     */
    protected function compareStrings($str1, $str2)
    {
        return strcasecmp(trim($str1), trim($str2));
    }
	
	/**
     * Extract Numbers from a String
     *
     * @param $extns
     * @return int
     */
    protected function extractNumbers($extns)
    {
        return preg_replace('/[^0-9]/', '', $extns);
    }

    /**
     * Validate address information against what's there in the order
     *
     * @param Order $order
     * @return bool
     */
    protected function validateAddress(Order $order)
    {
		$addressTypeToPass = $this->addressconfig->getAddressTypeToPass($order->getStoreId());
		
		if ($addressTypeToPass == AbstractAddress::TYPE_SHIPPING && !$order->getIsVirtual()) {
            $addressType = $order->getShippingAddress();
        } else {
            $addressType = $order->getBillingAddress();
        }
		
        // check ZIP
        if ($this->reader->hasAddressZip()
            && $this->compareStrings(substr($this->reader->getAddressZip(), 0, 5), substr($addressType->getPostcode(), 0, 5)) != 0) {
            return false;
        }

        // check street
        $streetArr = $addressType->getStreet();
        $street1 = isset($streetArr[0]) ? $streetArr[0] : '';
        $street2 = isset($streetArr[1]) ? $streetArr[1] : '';
        $responseStreet1 = $this->reader->hasAddressLine1() && $this->reader->getAddressLine1() != 'null'
            ? $this->reader->getAddressLine1() : '';
        $responseStreet2 = $this->reader->hasAddressLine2() && $this->reader->getAddressLine2() != 'null'
            ? $this->reader->getAddressLine2() : '';
        if ($this->reader->hasAddressLine1()
            && ($this->compareStrings($this->extractNumbers($responseStreet1), $this->extractNumbers($street1)) != 0
            || $this->compareStrings($this->extractNumbers($responseStreet2), $this->extractNumbers($street2)) != 0)) {
            return false;
        }

        return true;
    }
}
