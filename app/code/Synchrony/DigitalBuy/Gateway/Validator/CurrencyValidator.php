<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class CurrencyValidator
 * @package Synchrony\DigitalBuy\Gateway\Validator
 */
class CurrencyValidator extends AbstractValidator
{
    /**
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    /**
     * CurrencyValidator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param ConfigInterface $config
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        ConfigInterface $config
    ) {
        $this->config = $config;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $allowedCurrency = $this->config->getAllowedCurrency();
        if (in_array($validationSubject['currency'], $allowedCurrency)) {
            return $this->createResult(
                true,
                ['status' => 200]
            );
        }

        return $this->createResult(
            false,
            [__('The currency selected is not supported by Synchrony Digital.')]
        );
    }
}
