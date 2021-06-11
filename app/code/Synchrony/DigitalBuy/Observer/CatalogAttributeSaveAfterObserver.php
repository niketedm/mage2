<?php

namespace Synchrony\DigitalBuy\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CatalogAttributeSaveAfterObserver implements ObserverInterface
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
     * After save attribute if it is not used for promo rules already check rules for containing this attribute
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules()) {
            $this->checkPromoRulesAvailability->checkPromoRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }
}
