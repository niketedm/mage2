<?php

namespace Synchrony\DigitalBuy\Gateway\Validator\Installment;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Synchrony\DigitalBuy\Gateway\Validator\AbstractAuthorizationResponseValidator;

/**
 * Class Installment AuthorizationResponseValidator
 */
class AuthorizationResponseValidator extends AbstractAuthorizationResponseValidator
{
    /**
     * Validate Response Data
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
            if (!$this->reader->hasSettlementId() || !$this->reader->getSettlementId()) {
                return $this->createResult(false, ['Settlement Id is missing from response']);
            }

            if (!$this->reader->hasAccountToken() || !$this->reader->getAccountToken()) {
                return $this->createResult(false, ['Account Token is missing from response']);
            }

            if (!$this->reader->hasTransactionAmount() || !$this->reader->getTransactionAmount()) {
                return $this->createResult(false, ['Transaction amount is missing from response']);
            }

            if (!$this->reader->hasPromoCode() || !$this->reader->getPromoCode()) {
                return $this->createResult(false, ['Promo Code is missing from response']);
            }

            $amountDue = SubjectReader::readAmount($validationSubject);
            if (abs($amountDue - floatval($this->reader->getTransactionAmount())) > 0.0001) {
                return $this->createResult(false, ['Transaction amount doesn\'t match order amount']);
            }
            return $this->createResult(true);
        }

        if ($this->isUserTerminated()) {
            return $this->createResult(false, ['Terminated by user'], self::STATUS_TERMINATED);
        } elseif ($this->isTokenExpired()) {
            return $this->createResult(false, ['Token expired'], self::STATUS_TOKEN_EXPIRED);
        } elseif ($this->isAuthorizationDeclined()) {
            return $this->createResult(false, ['Authorization declined'], self::STATUS_AUTHORIZATION_DECLINED);
        } elseif ($this->isApplyProcessError()) {
            return $this->createResult(false, ['Apply Process Error'], self::APPLY_PROCESS_ERROR);
        }

        return $this->createResult(false, ['Unknown status code']);
    }

    /**
     * Check if status code is indicates that user application is resulted in error
     *
     * @return bool
     */
    protected function isApplyProcessError()
    {
        return in_array($this->reader->getStatusCode(), self::APPLY_PROCESS_ERROR, true);
    }
}
