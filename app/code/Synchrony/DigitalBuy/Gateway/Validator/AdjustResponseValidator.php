<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Synchrony\DigitalBuy\Gateway\Response\BuyServiceResponseReader;

/**
 * Class  Adjust Response Validator
 */
class AdjustResponseValidator implements ValidatorInterface
{
    const REFUND_APPROVED_STATUS_CODES = ['000'];
    const REFUND_DECLINED_STATUS_CODE = '001';

    const REFUND_CALL_FOR_AUTH_STATUS_CODE = '002';
    const REFUND_ERROR_CODE = '999';

    const REFUND_ERROR_RESPONSE_CODES=['200','300','400'];

    const STATUS_REFUND_DECLINED_ = 'declined';
    const STATUS_REFUND_CALL_FOR_AUTH = 'call_for_auth';
    const STATUS_REFUND_ERROR = 'error';
    const STATUS_REFUND_ERROR_RESPONSE_CODES = 'refund_error_response';

    /**
     * @var BuyServiceResponseReader
     */
    private $reader;

    /**
     * @var Result\ValidatorResultFactory
     */
    private $resultInterfaceFactory;

    /**
     * constructor.
     * @param BuyServiceResponseReader $reader
     * @param Result\ValidatorResultFactory $resultFactory
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
     * @return Result\StatusInquiryValidatorResult
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

        if ($this->isRefundDeclined()) {
            return $this->createResult(false, ['REFUND DECLINED'], self::STATUS_REFUND_DECLINED_);
        } elseif ($this->isRefundError()) {
            return $this->createResult(false, ['REFUND CALL FOR AUTH'], self::STATUS_REFUND_ERROR);
        } elseif ($this->isRefundCallForAuth()) {
            return $this->createResult(false, ['REFUND ERROR'], self::STATUS_REFUND_CALL_FOR_AUTH);
        }

        if ($this->isRefundResponseErrorCode()) {
            if ($this->reader->hasErrors() || $this->reader->getErrors()) {
                return $this->createResult(false, $this->reader->getErrors(), self::STATUS_REFUND_ERROR_RESPONSE_CODES);
            }

            if ($this->reader->hasResponseText() || $this->reader->getResponseText()) {
                return $this->createResult(false, [$this->reader->getResponseText()], self::STATUS_REFUND_ERROR_RESPONSE_CODES);
            }

            return $this->createResult(false, ['Error Code:'.$this->reader->getResponseCode()]);
        }

        if ($this->isRefundSuccessful()) {
            return $this->createResult(true);
        }
        
        return $this->createResult(false, ['Unknown Response code']);
    }

    /**
     *  Check if status code indicates that refund has been declined
     *
     * @return bool
     */
    protected function isRefundDeclined()
    {
        return $this->reader->getStatusCode() === self::REFUND_DECLINED_STATUS_CODE;
    }

    /**
     * Check if response code indicates call for auth
     *
     * @return bool
     */
    protected function isRefundCallForAuth()
    {
        return $this->reader->getResponseCode() === self::REFUND_CALL_FOR_AUTH_STATUS_CODE;
    }

    /**
     * Check if response code indicates refund error
     *
     * @return bool
     */
    protected function isRefundError()
    {
        return $this->reader->getResponseCode() === self::REFUND_ERROR_CODE;
    }
    
    /**
     * Check if response code indicates error
     *
     * @return bool
     */
    protected function isRefundResponseErrorCode()
    {
        return in_array($this->reader->getResponseCode(), self::REFUND_ERROR_RESPONSE_CODES, true);
    }

    /**
     * Check if refund is successful
     *
     * @return bool
     */
    protected function isRefundSuccessful()
    {
        return in_array($this->reader->getResponseCode(), self::REFUND_APPROVED_STATUS_CODES, true);
    }
}
