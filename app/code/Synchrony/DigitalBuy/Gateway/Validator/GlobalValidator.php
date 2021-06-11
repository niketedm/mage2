<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class GlobalValidator extends AbstractValidator
{
    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $infoInstance = $validationSubject['payment'];
        if ($infoInstance instanceof \Magento\Quote\Model\Quote\Payment
            && $infoInstance->getMethodInstance() && $infoInstance->getQuote()) {
            /** @var \Synchrony\DigitalBuy\Model\Payment\Method\Adapter $method */
            $method = $infoInstance->getMethodInstance();
            if (!$method->canUseForCart($infoInstance->getQuote())) {
                return $this->createResult(false, [$method->getLastCartValidationMsg()]);
            }
        }
        return $this->createResult(true);
    }
}
