<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Synchrony\DigitalBuy\Gateway\Response\StatusInquiryReader;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;

class AuthenticationStatusInquiryResponseValidator extends AbstractStatusInquiryResponseValidator
{
    /**
     * Successful status codes
     */
    const AUTH_SUCCESS_STATUS_CODES = ['002', '003', '00', '21', '22', '24', '25', '26', '27', '28'];

    /**
     * Validate authentication response
     *
     * @param array $commandSubject
     * @return Result\ValidatorResult
     */
    public function validate(array $commandSubject)
    {
        $response = SubjectReader::readResponse($commandSubject);
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

        if ($this->isAuthSuccessful()) {
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
        }

        return $this->createResult(false, ['Unknown status code']);
    }

    /**
     * Check if status code is successful for Account Authentication Status Inquiry
     *
     * @return bool
     */
    protected function isAuthSuccessful()
    {
        return in_array($this->reader->getStatusCode(), self::AUTH_SUCCESS_STATUS_CODES, true);
    }
}
