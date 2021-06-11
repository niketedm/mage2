<?php

namespace Synchrony\DigitalBuy\Model\Rule;

/**
 * Synchrony Promotion Validator Model
 */
class Validator extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var int
     */
    private $websiteId;

    /**
     * @var int
     */
    private $customerGroupId;

    /**
     * @var \Synchrony\DigitalBuy\Model\ResourceModel\Rule\Collection
     */
    private $rules;

    /**
     * @var \Synchrony\DigitalBuy\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var  \Synchrony\DigitalBuy\Model\Rule\ChildrenValidationLocator
     */
    private $childrenValidationLocator;

    /**
     * @param \Synchrony\DigitalBuy\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Synchrony\DigitalBuy\Model\Rule\ChildrenValidationLocator
     */
    public function __construct(
        \Synchrony\DigitalBuy\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Synchrony\DigitalBuy\Model\Rule\ChildrenValidationLocator $childrenValidationLocator
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->childrenValidationLocator = $childrenValidationLocator;
    }

    /**
     * Init validator
     * Init process load collection of rules for specific website,
     *
     * @param int $websiteId
     * @param int $customerGroupId
     */
    public function init($websiteId, $customerGroupId)
    {
        $this->websiteId = $websiteId;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * Get rules collection for current object state
     *
     * @return \Synchrony\DigitalBuy\Model\ResourceModel\Rule\Collection
     */
    private function getRules()
    {
        if (!isset($this->rules)) {
            $this->rules = $this->collectionFactory->create()
                ->addWebsiteFilter($this->websiteId)
                ->addCustomerGroupFilter($this->customerGroupId)
                ->addDateFilter()
                ->addFieldToFilter('is_active', 1)
                ->setPriorityOrder()
                ->load();
        }
        return $this->rules;
    }

    /**
     * Apply rules to current order item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return \Synchrony\DigitalBuy\Model\Rule|bool
     */
    public function process(\Magento\Sales\Model\Order\Item $item)
    {
        $order = $item->getOrder();
        foreach ($this->getRules() as $rule) {
            if (!$this->canProcessRule($rule, $order)) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                if (!$this->childrenValidationLocator->isChildrenValidationRequired($item)) {
                    continue;
                }
                $childItems = $item->getChildrenItems();
                $isContinue = true;
                if (!empty($childItems)) {
                    foreach ($childItems as $childItem) {
                        if ($rule->getActions()->validate($childItem)) {
                            $isContinue = false;
                        }
                    }
                }
                if ($isContinue) {
                    continue;
                }
            }

            return $rule;
        }

        return false;
    }

    /**
     * Checks whether rule can be applied to order
     *
     * @param \Synchrony\DigitalBuy\Model\Rule $rule
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    private function canProcessRule($rule, $order)
    {
        if ($rule->hasIsValidForOrder($order)) {
            return $rule->getIsValidForOrder($order);
        }

        $validationResult = (bool) $rule->validate($order);
        $rule->setIsValidForOrder($order, $validationResult);

        return $validationResult;
    }
}
