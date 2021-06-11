<?php

namespace Synchrony\DigitalBuy\Gateway\Validator\Result;

class ValidatorResult extends \Magento\Payment\Gateway\Validator\Result
{
    /**
     * @var string
     */
    private $failCode;

    /**
     * ValidatorResult constructor.
     * @param bool $isValid
     * @param array $failsDescription
     * @param null $failCode
     */
    public function __construct(bool $isValid, array $failsDescription = [], $failCode = null)
    {
        $this->failCode = $failCode;
        parent::__construct($isValid, $failsDescription);
    }

    /**
     * @return null|string
     */
    public function getFailCode()
    {
        return $this->failCode;
    }
}
