<?php

namespace Synchrony\DigitalBuy\Api\Data;

/**
 * Interface RuleInterface
 *
 * @api
 */
interface RuleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Return rule id
     *
     * @return int|null
     */
    public function getRuleId();

    /**
     * Set rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId);

    /**
     * Get rule name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set rule name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get rule name
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Set rule name
     *
     * @param string $name
     * @return $this
     */
    public function setCode($name);

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get a list of websites the rule applies to
     *
     * @return int[]
     */
    public function getWebsiteIds();

    /**
     * Set the websites the rule applies to
     *
     * @param int[] $websiteIds
     * @return $this
     */
    public function setWebsiteIds(array $websiteIds);

    /**
     * Get the start date when the coupon is active
     *
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set the star date when the coupon is active
     *
     * @param string $fromDate
     * @return $this
     */
    public function setFromDate($fromDate);

    /**
     * Get the end date when the coupon is active
     *
     * @return string|null
     */
    public function getToDate();

    /**
     * Set the end date when the coupon is active
     *
     * @param string $fromDate
     * @return $this
     */
    public function setToDate($fromDate);

    /**
     * Whether the coupon is active
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsActive();

    /**
     * Set whether the coupon is active
     *
     * @param bool $isActive
     * @return bool
     */
    public function setIsActive($isActive);

    /**
     * Get condition for the rule
     *
     * @return \Synchrony\DigitalBuy\Api\Data\ConditionInterface|null
     */
    public function getCondition();

    /**
     * Set condition for the rule
     *
     * @param \Synchrony\DigitalBuy\Api\Data\ConditionInterface|null $condition
     * @return $this
     */
    public function setCondition(\Synchrony\DigitalBuy\Api\Data\ConditionInterface $condition = null);

    /**
     * Get action condition
     *
     * @return \Synchrony\DigitalBuy\Api\Data\ConditionInterface|null
     */
    public function getActionCondition();

    /**
     * Set action condition
     *
     * @param \Synchrony\DigitalBuy\Api\Data\ConditionInterface|null $actionCondition
     * @return $this
     */
    public function setActionCondition(\Synchrony\DigitalBuy\Api\Data\ConditionInterface $actionCondition = null);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Synchrony\DigitalBuy\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Synchrony\DigitalBuy\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Synchrony\DigitalBuy\Api\Data\RuleExtensionInterface $extensionAttributes);
}
