<?php

namespace Synchrony\DigitalBuy\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Synchrony\DigitalBuy\Api\Data\RuleInterface;
use Synchrony\DigitalBuy\Api\Data\ConditionInterface;
use Magento\Sales\Model\Order;

class Rule extends \Magento\Rule\Model\AbstractModel implements RuleInterface
{
    const CURRENT_RULE_REGISTRY_KEY = 'current_synchrony_promotion';

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
    const KEY_CUSTOMER_GROUPS = 'customer_group_ids';
    const KEY_SORT_ORDER = 'sort_order';

    /**
     * Orders validation result cache
     *
     * @var array
     */
    protected $validatedOrders = [];

    /**
     * @var \Synchrony\DigitalBuy\Model\Rule\Condition\CombineFactory
     */
    protected $_condCombineFactory;

    /**
     * @var \Synchrony\DigitalBuy\Model\Rule\Condition\Product\CombineFactory
     */
    protected $_condProdCombineF;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Synchrony\DigitalBuy\Model\Rule\Condition\CombineFactory $condCombineFactory
     * @param \Synchrony\DigitalBuy\Model\Rule\Condition\Product\CombineFactory $condProdCombineF
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param ExtensionAttributesFactory|null $extensionFactory
     * @param AttributeValueFactory|null $customAttributeFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Synchrony\DigitalBuy\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Synchrony\DigitalBuy\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory $customAttributeFactory = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {

        $this->_condCombineFactory = $condCombineFactory;
        $this->_condProdCombineF = $condProdCombineF;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Synchrony\DigitalBuy\Model\ResourceModel\Rule::class);
        $this->setIdFieldName('rule_id');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Synchrony\DigitalBuy\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Synchrony\DigitalBuy\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->_condProdCombineF->create();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId($formName = '')
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }

    /**
     * Check cached validation result for specific order
     *
     * @param Order $order
     * @return bool
     */
    public function hasIsValidForOrder($order)
    {
        return isset($this->validatedOrders[$order->getIncrementId()]) ? true : false;
    }

    /**
     * Set validation result for specific order to results cache
     *
     * @param Order $order
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForOrder($order, $validationResult)
    {
        if ($order->getIncrementId()) {
            $this->validatedOrders[$order->getIncrementId()] = $validationResult;
        }
        return $this;
    }

    /**
     * Get cached validation result for specific order
     *
     * @param Order $order
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidForOrder($order)
    {
        return isset($this->validatedOrders[$order->getIncrementId()])
            ? $this->validatedOrders[$order->getIncrementId()] : false;
    }

    /**
     * Return rule id
     *
     * @return int|null
     */
    public function getRuleId()
    {
        return $this->getData(self::KEY_RULE_ID);
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
        return $this->getData(self::KEY_NAME);
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
     * Get rule name
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::KEY_CODE);
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
     * Get description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::KEY_DESCRIPTION);
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
     * Get the start date when the coupon is active
     *
     * @return string|null
     */
    public function getFromDate()
    {
        return $this->getData(self::KEY_FROM_DATE);
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
        return $this->getData(self::KEY_TO_DATE);
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
        return $this->getData(self::KEY_IS_ACTIVE);
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
        return $this->getData(self::KEY_CONDITION);
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
        return $this->getData(self::KEY_ACTION_CONDITION);
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
        return $this->getData(self::KEY_WEBSITES);
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
     * Get ids of customer groups that the rule applies to
     *
     * @return int[]
     */
    public function getCustomerGroupIds()
    {
        return $this->getData(self::KEY_CUSTOMER_GROUPS);
    }

    /**
     * Set the customer groups that the rule applies to
     *
     * @param int[] $customerGroups
     * @return $this
     */
    public function setCustomerGroupIds(array $customerGroups)
    {
        return $this->setData(self::KEY_CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(self::KEY_SORT_ORDER);
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
