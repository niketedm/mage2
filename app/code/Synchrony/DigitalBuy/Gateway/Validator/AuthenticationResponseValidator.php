<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Synchrony\DigitalBuy\Gateway\Response\AuthenticationReader;

class AuthenticationResponseValidator extends AbstractValidator
{
    /**
     * @var AuthenticationReader
     */
    private $reader;

    /**
     * AuthenticationResponseValidator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param AuthenticationReader $reader
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        AuthenticationReader $reader
    ) {
        parent::__construct($resultFactory);
        $this->reader = $reader;
    }

    /**
     * Validate authentication response
     *
     * @param array $commandSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $commandSubject)
    {
        $response = $commandSubject['response'];
        $this->reader->setData($response);
        if (!$this->reader->hasClientToken() || !$this->reader->getClientToken()) {
            return $this->createResult(false, ['Client token is missing from response']);
        }
        return $this->createResult(true);
    }
}
