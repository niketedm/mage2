<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Synchrony\DigitalBuy\Gateway\Response\StatusInquiryReader;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig as AddressConfig;

abstract class AbstractStatusInquiryResponseValidator implements ValidatorInterface
{
    /**
     * Response codes that should be considered successful
     */
    const SUCCESS_RESPONSE_CODES = ['000'];

    /**
     * Flow status codes
     */
    const TOKEN_EXPIRED_STATUS_CODE = '401';
    const USER_TERMINATED_STATUS_CODE = '100';

    /**
     * Exception status code
     */
    const SYSTEM_EXCEPTION_STATUS_CODE = '500';

    /**
     * Successful status codes
     */
    const APPLY_PENDING_STATUS_CODES = ['04', '06', '99'];
    const APPLY_DECLINED_STATUS_CODES = ['07', '08', '12', '13', '14', '15', '16', '17', '18', '81', '82', '90'];
    const APPLY_PROCESS_ERROR = ['03','10','11'];

    /**
     * Internal aliases for status
     */
    const STATUS_TERMINATED = 'terminated';
    const STATUS_TOKEN_EXPIRED = 'expired';
    const STATUS_APPLICATION_PENDING = 'pending';
    const STATUS_APPLICATION_DECLINED = 'declined';

    /**
     * @var Result\ValidatorResultFactory
     */
    protected $resultInterfaceFactory;

    /**
     * @var StatusInquiryReader
     */
    protected $reader;
	
	/**
     * @var AddressConfig
     */
    protected $addressconfig;

    /**
     * AuthenticationResponseValidator constructor.
     * @param Result\ValidatorResultFactory $resultFactory
     * @param StatusInquiryReader $reader
     */
    public function __construct(
        Result\ValidatorResultFactory $resultFactory,
        StatusInquiryReader $reader,
		AddressConfig $addressconfig
    ) {
        $this->resultInterfaceFactory = $resultFactory;
        $this->reader = $reader;
		$this->addressconfig = $addressconfig;
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
     * Check if synchrony response code is successful
     *
     * @return bool
     */
    protected function isSuccessResponseCode()
    {
        return in_array($this->reader->getResponseCode(), self::SUCCESS_RESPONSE_CODES, true);
    }

    /**
     * Check if status code indicates that user has terminated modal process
     *
     * @return bool
     */
    protected function isUserTerminated()
    {
        return $this->reader->getStatusCode() === self::USER_TERMINATED_STATUS_CODE;
    }

    /**
     * Check if status code indicates that token has expired
     *
     * @return bool
     */
    protected function isTokenExpired()
    {
        return $this->reader->getStatusCode() === self::TOKEN_EXPIRED_STATUS_CODE;
    }

    /**
     * Check if status code is indicates that user application has been declined
     *
     * @return bool
     */
    protected function isApplicationDeclined()
    {
        return in_array($this->reader->getStatusCode(), self::APPLY_DECLINED_STATUS_CODES, true);
    }

    /**
     * Check if status code is indicates that user application is pending
     *
     * @return bool
     */
    protected function isApplicationPending()
    {
        return in_array($this->reader->getStatusCode(), self::APPLY_PENDING_STATUS_CODES, true);
    }

    /**
     * Check if status code idicates an exception on Synchrony side
     *
     * @return bool
     */
    protected function isExceptionStatusCode()
    {
        return $this->reader->getStatusCode() === self::SYSTEM_EXCEPTION_STATUS_CODE;
    }
}
