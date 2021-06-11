<?php

namespace Synchrony\DigitalBuy\Model\Data;

use Synchrony\DigitalBuy\Api\Data\ConditionInterface;
use Synchrony\DigitalBuy\Api\Data\RuleInterface;

/**
 * Class Rule
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @codeCoverageIgnore
 */
class Rule extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Synchrony\DigitalBuy\Api\Data\RuleInterface
{
    const KEY_RULE_ID = 'rule_id';
    const KEY_NAME = 'name';
    const KEY_CODE = 'code';
    const KEY_DESCRIPTION = 'description';
    const KEY_FROM_DATE = 'from_date';
    const KEY_TO_DATE = 'to_date';
    const KEY_IS_ACTIVE = 'is_active';
    const KEY_CONDITION = 'condition';
    const KEY_ACTION_CONDITION = 'action_condition';
    const KEY_WEBSITES = 'website_ids';
    const KEY_SORT_ORDER = 'sort_order';

    /**
     * Return rule id
     *
     * @return int|null
     */
    public function getRuleId()
    {
        return $this->_get(self::KEY_RULE_ID);
    }

    /**
     * Set rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::KEY_RULE_ID, $ruleId);
    }

    /**
     * Get rule name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::KEY_NAME);
    }

    /**
     * Set rule name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::KEY_DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::KEY_DESCRIPTION, $description);
    }

    /**
     * Get rule name
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->_get(self::KEY_CODE);
    }

    /**
     * Set rule name
     *
     * @param string $name
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::KEY_CODE, $code);
    }

    /**
     * Get the start date when the coupon is active
     *
     * @return string|null
     */
    public function getFromDate()
    {
        return $this->_get(self::KEY_FROM_DATE);
    }

    /**
     * Set the star date when the coupon is active
     *
     * @param string $fromDate
     * @return $this
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::KEY_FROM_DATE, $fromDate);
    }

    /**
     * Get the end date when the coupon is active
     *
     * @return string|null
     */
    public function getToDate()
    {
        return $this->_get(self::KEY_TO_DATE);
    }

    /**
     * Set the end date when the coupon is active
     *
     * @param string $toDate
     * @return $this
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::KEY_TO_DATE, $toDate);
    }

    /**
     * Whether the rule is active
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsActive()
    {
        return $this->_get(self::KEY_IS_ACTIVE);
    }

    /**
     * Set whether the coupon is active
     *
     * @param bool $isActive
     * @return bool
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::KEY_IS_ACTIVE, $isActive);
    }

    /**
     * Get condition for the rule
     *
     * @return \Synchrony\DigitalBuy\Api\Data\ConditionInterface|null
     */
    public function getCondition()
    {
        return $this->_get(self::KEY_CONDITION);
    }

    /**
     * Set condition for the rule
     *
     * @param \Synchrony\DigitalBuy\Api\Data\ConditionInterface|null $condition
     * @return $this
     */
    public function setCondition(ConditionInterface $condition = null)
    {
        return $this->setData(self::KEY_CONDITION, $condition);
    }

    /**
     * Get action condition
     *
     * @return \Synchrony\DigitalBuy\Api\Data\ConditionInterface|null
     */
    public function getActionCondition()
    {
        return $this->_get(self::KEY_ACTION_CONDITION);
    }

    /**
     * Set action condition
     *
     * @param \Synchrony\DigitalBuy\Api\Data\ConditionInterface|null $actionCondition
     * @return $this
     */
    public function setActionCondition(ConditionInterface $actionCondition = null)
    {
        return $this->setData(self::KEY_ACTION_CONDITION, $actionCondition);
    }

    /**
     * Get a list of websites the rule applies to
     *
     * @return int[]
     */
    public function getWebsiteIds()
    {
        return $this->_get(self::KEY_WEBSITES);
    }

    /**
     * Set the websites the rule applies to
     *
     * @param int[] $websites
     * @return $this
     */
    public function setWebsiteIds(array $websites)
    {
        return $this->setData(self::KEY_WEBSITES, $websites);
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_get(self::KEY_SORT_ORDER);
    }

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::KEY_SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Synchrony\DigitalBuy\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Synchrony\DigitalBuy\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Synchrony\DigitalBuy\Api\Data\RuleExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
