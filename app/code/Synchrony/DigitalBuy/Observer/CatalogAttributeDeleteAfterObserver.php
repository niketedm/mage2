<?php

namespace Synchrony\DigitalBuy\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CatalogAttributeDeleteAfterObserver implements ObserverInterface
{
    /**
     * @var CheckPromoRulesAvailability
     */
    protected $checkPromoRulesAvailability;

    /**
     * @param CheckPromoRulesAvailability $checkPromoRulesAvailability
     */
    public function __construct(
        CheckPromoRulesAvailability $checkPromoRulesAvailability
    ) {
        $this->checkPromoRulesAvailability = $checkPromoRulesAvailability;
    }

    /**
     * After delete attribute check rules that contains deleted attribute
     * If rules was found they will seted to inactive and added notice to admin session
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getIsUsedForPromoRules()) {
            $this->checkPromoRulesAvailability->checkPromoRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }
}
