<?php

namespace Synchrony\DigitalBuy\Gateway\Validator\Installment;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Synchrony\DigitalBuy\Gateway\Response\Installment\CaptureResponseReader;
use Synchrony\DigitalBuy\Gateway\Validator\Result\ValidatorResultFactory;

/**
 * Class  Installment Capture Response Validator
 */
class CaptureResponseValidator implements ValidatorInterface
{
    /**
     * @var ForcePurchaseResponseReader
     */
    private $reader;

    /**
     * @var Result\ValidatorResultFactory
     */
    private $resultInterfaceFactory;

    /**
     * CaptureResponseValidator constructor
     *
     * @param CaptureResponseReader $reader
     * @param ValidatorResultFactory $resultFactory
     */
    public function __construct(
        CaptureResponseReader $reader,
        ValidatorResultFactory $resultFactory
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
    protected function createResult($isValid = false, array $fails = [], $failCode = null)
    {
        return $this->resultInterfaceFactory->create(
            [
                'isValid' => (bool) $isValid,
                'failsDescription' => $fails,
                'failCode' => $failCode
            ]
        );
    }

    /**
     * Validate Capture Response
     *
     * @param array $validationSubject
     * @return Result\ValidatorResult
     */
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);
        $this->reader->setData($response);

        if (!$this->reader->getTransactionId()) {
            return $this->createResult(false, ['Transaction Id is missing from response']);
        }

        return $this->createResult(true);
    }
}
