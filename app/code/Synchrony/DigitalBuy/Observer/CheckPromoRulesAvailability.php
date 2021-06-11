<?php

namespace Synchrony\DigitalBuy\Observer;

class CheckPromoRulesAvailability
{
    /**
     * @var \Synchrony\DigitalBuy\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @param \Synchrony\DigitalBuy\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Synchrony\DigitalBuy\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     * @return $this
     */
    public function checkPromoRulesAvailability($attributeCode)
    {
        /* @var $collection \Synchrony\DigitalBuy\Model\ResourceModel\Rule\Collection */
        $collection = $this->collectionFactory->create()->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            /* @var $rule \Synchrony\DigitalBuy\Model\Rule */
            $rule->setIsActive(0);
            /* @var $rule->getConditions() \Synchrony\DigitalBuy\Model\Rule\Condition\Combine */
            $this->removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $this->removeAttributeFromConditions($rule->getActions(), $attributeCode);
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            $this->messageManager->addWarning(
                __(
                    '%1 Synchrony Promotions based on "%2" attribute have been disabled.',
                    $disabledRulesCount,
                    $attributeCode
                )
            );
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param \Magento\Rule\Model\Condition\Combine $combine
     * @param string $attributeCode
     * @return void
     */
    private function removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof \Magento\Rule\Model\Condition\Combine) {
                $this->removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof \Synchrony\DigitalBuy\Model\Rule\Condition\Product) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }
        $combine->setConditions($conditions);
    }
}
