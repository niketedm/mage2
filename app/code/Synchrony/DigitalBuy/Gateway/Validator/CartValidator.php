<?php

namespace Synchrony\DigitalBuy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class CartValidator
 * @package Synchrony\DigitalBuy\Gateway\Validator
 */
class CartValidator extends AbstractValidator
{
    /**
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    /**
     * CartValidator constructor.
     *
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
        $isValid = true;
        /** @var \Magento\Payment\Model\MethodInterface $payment */
        $payment = $validationSubject['payment'];
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $validationSubject['quote'];
        $storeId = $quote->getStoreId();
        $prohibitedAttributedSets = $this->config->getProhibitedProductAttributeSets($storeId);
        $cartItems = $quote->getAllItems();
        $prohibitedProductNames = [];
        foreach ($cartItems as $item) {
            $visibleItem = $item;
            if ($item->getParentItem()) {
                $visibleItem = $item->getParentItem();
            }
            if (in_array($item->getProduct()->getAttributeSetId(), $prohibitedAttributedSets)
                && $visibleItem->getBaseRowTotalInclTax() > 0.0001) {
                $isValid = false;
                $prohibitedProductNames[$visibleItem->getId()] = $visibleItem->getProduct()->getName();
            }
        }
        $validationMessage = '';
        if (!$isValid) {
            $paymentMethodTitle = $payment->getTitle();
            if (count($prohibitedProductNames) > 1) {
                $finalProductName = array_pop($prohibitedProductNames);
                $validationMessage = __(
                    '%1 and %2 items are not eligible for %3 financing',
                    implode(', ', $prohibitedProductNames),
                    $finalProductName,
                    $paymentMethodTitle
                );
            } else {
                $validationMessage = __(
                    '%1 item is not eligible for %2 financing.',
                    implode(',', $prohibitedProductNames),
                    $paymentMethodTitle
                );
            }
        }
        return $this->createResult($isValid, [$validationMessage]);
    }
}
