<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Synchrony\DigitalBuy\Gateway\Response\BuyServiceResponseReader;

/**
 * Class  Adjust Response Validator
 */
class ForcePurchaseResponseValidator implements ValidatorInterface
{
    const CAPTURE_APPROVED_STATUS_CODES = ['000'];
    const CAPTURE_DECLINED_STATUS_CODE = '001';

    const CAPTURE_CALL_FOR_AUTH_STATUS_CODE = '002';
    const CAPTURE_ERROR_CODE = '999';

    const CAPTURE_ERROR_RESPONSE_CODES = ['200','300','400'];

    const STATUS_CAPTURE_DECLINED = 'capture_declined';
    const STATUS_CAPTURE_CALL_FOR_AUTH = 'capture_call_for_auth';
    const STATUS_CAPTURE_ERROR = 'capture_error';
    const STATUS_CAPTURE_ERROR_RESPONSE_CODES = 'capture_error_response';

    /**
     * @var ForcePurchaseResponseReader
     */
    private $reader;

    /**
     * @var Result\ValidatorResultFactory
     */
    private $resultInterfaceFactory;

    /**
     * constructor.
     * @param BuyServiceResponseReader $reader
     * @param ResultInterfaceFactory $resultFactory
     */
    public function __construct(
        BuyServiceResponseReader $reader,
        Result\ValidatorResultFactory $resultFactory
    ) {
        $this->reader = $reader;
        $this->resultInterfaceFactory = $resultFactory;
    }

    /**
     * Factory method
     *
     * @param bool $isValid
     * @param array $fails
     * @return Result\ValidatorResult
     */
    protected function createResult($isValid, array $fails = [], $failCode = null)
    {
        return $this->resultInterfaceFactory->create(
            [
                'isValid' => (bool)$isValid,
                'failsDescription' => $fails,
                'failCode' => $failCode
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);
        $this->reader->setData($response);

        if (!$this->reader->hasResponseCode() || !$this->reader->getResponseCode()) {
            if ($this->reader->hasFaultCode() || $this->reader->getFaultCode()) {
                if ($this->reader->hasFaultString() || $this->reader->getFaultString()) {
                    return $this->createResult(false, [$this->reader->getFaultString()]);
                }
                return $this->createResult(false, [$this->reader->getFaultCode()]);
            }
            return $this->createResult(false, ['Response code is missing from response']);
        }

        if ($this->isCaptureDeclined()) {
            return $this->createResult(false, ['CAPTURE DECLINED'], self::STATUS_CAPTURE_DECLINED);
        } elseif ($this->isCaptureError()) {
            return $this->createResult(false, ['CAPTURE CALL FOR AUTH'], self::STATUS_CAPTURE_ERROR);
        } elseif ($this->isCaptureCallForAuth()) {
            return $this->createResult(false, ['CAPTURE ERROR'], self::STATUS_CAPTURE_CALL_FOR_AUTH);
        }

        if ($this->isCaptureResponseErrorCode()) {
            if ($this->reader->hasErrors() || $this->reader->getErrors()) {
                return $this->createResult(false, $this->reader->getErrors(), self::STATUS_CAPTURE_ERROR_RESPONSE_CODES);
            }

            if ($this->reader->hasResponseText() || $this->reader->getResponseText()) {
                return $this->createResult(false, [$this->reader->getResponseText()], self::STATUS_CAPTURE_ERROR_RESPONSE_CODES);
            }

            return $this->createResult(false, ['Error Code:'.$this->reader->getResponseCode()]);
        }

        if ($this->isCaptureSuccessful()) {
            return $this->createResult(true);
        }

        return $this->createResult(false, ['Unknown Response code']);
    }

    /**
     *  Check if status code indicates that capture has been declined
     *
     * @return bool
     */
    protected function isCaptureDeclined()
    {
        return $this->reader->getStatusCode() === self::CAPTURE_DECLINED_STATUS_CODE;
    }

    /**
     * Check if response code indicates call for auth
     *
     * @return bool
     */
    protected function isCaptureCallForAuth()
    {
        return $this->reader->getResponseCode() === self::CAPTURE_CALL_FOR_AUTH_STATUS_CODE;
    }

    /**
     * Check if response code indicates capture error
     *
     * @return bool
     */
    protected function isCaptureError()
    {
        return $this->reader->getResponseCode() === self::CAPTURE_ERROR_CODE;
    }

    /**
     * Check if response code indicates error
     *
     * @return bool
     */
    protected function isCaptureResponseErrorCode()
    {
        return in_array($this->reader->getResponseCode(), self::CAPTURE_ERROR_RESPONSE_CODES, true);
    }

    /**
     * Check if capture is success
     *
     * @return bool
     */
    protected function isCaptureSuccessful()
    {
        return in_array($this->reader->getResponseCode(), self::CAPTURE_APPROVED_STATUS_CODES, true);
    }
}
