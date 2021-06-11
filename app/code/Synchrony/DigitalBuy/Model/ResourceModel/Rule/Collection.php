<?php

namespace Synchrony\DigitalBuy\Model\ResourceModel\Rule;

use Magento\Framework\Serialize\Serializer\Json;

class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{

    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = \Synchrony\DigitalBuy\Model\ResourceModel\Rule::ASSOCIATED_ENTITIES_MAP;

    /**
     * @var Json $serializer
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $date;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param Json $serializer Optional parameter for backward compatibility
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        Json $serializer = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->date = $date;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
    }
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Synchrony\DigitalBuy\Model\Rule::class, \Synchrony\DigitalBuy\Model\ResourceModel\Rule::class);
        $this->_map['fields']['rule_id'] = 'main_table.rule_id';
    }

    /**
     * @param string $entityType
     * @param string $objectField
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function mapAssociatedEntities($entityType, $objectField)
    {
        if (!$this->_items) {
            return;
        }

        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        $ruleIdField = $entityInfo['rule_id_field'];
        $entityIds = $this->getColumnValues($ruleIdField);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table'])
        )->where(
            $ruleIdField . ' IN (?)',
            $entityIds
        );

        $associatedEntities = $this->getConnection()->fetchAll($select);

        array_map(function ($associatedEntity) use ($entityInfo, $ruleIdField, $objectField) {
            $item = $this->getItemByColumnValue($ruleIdField, $associatedEntity[$ruleIdField]);
            $itemAssociatedValue = $item->getData($objectField) === null ? [] : $item->getData($objectField);
            $itemAssociatedValue[] = $associatedEntity[$entityInfo['entity_id_field']];
            $item->setData($objectField, $itemAssociatedValue);
        }, $associatedEntities);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->mapAssociatedEntities('website', 'website_ids');
        $this->mapAssociatedEntities('customer_group', 'customer_group_ids');

        $this->setFlag('add_websites_to_result', false);
        return parent::_afterLoad();
    }

    /**
     * Limit rules collection by specific customer group
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroupId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('customer_group');
        if (!$this->getFlag('is_customer_group_joined')) {
            $this->setFlag('is_customer_group_joined', true);
            $cond = 'main_table.' . $entityInfo['rule_id_field'] . ' = customer_group.' . $entityInfo['rule_id_field']
                . ' AND customer_group.' . $entityInfo['entity_id_field'] . ' = ?';
            $cond = $this->getConnection()->quoteInto($cond, $customerGroupId);

            $this->getSelect()->join(
                ['customer_group' => $this->getTable($entityInfo['associations_table'])],
                $cond,
                []
            );
        }
        return $this;
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     * @return $this
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr($this->serializer->serialize(['attribute' => $attributeCode]), 1, -1));
        /**
         * Information about conditions and actions stored in table as JSON encoded array
         * in fields conditions_serialized and actions_serialized.
         * If you want to find rules that contains some particular attribute, the easiest way to do so is serialize
         * attribute code in the same way as it stored in the serialized columns and execute SQL search
         * with like condition.
         * Table
         * +-------------------------------------------------------------------+
         * |     conditions_serialized       |         actions_serialized      |
         * +-------------------------------------------------------------------+
         * | {..."attribute":"attr_name"...} | {..."attribute":"attr_name"...} |
         * +---------------------------------|---------------------------------+
         * From attribute code "attr_code", will be generated such SQL:
         * `condition_serialized` LIKE '%"attribute":"attr_name"%'
         *      OR `actions_serialized` LIKE '%"attribute":"attr_name"%'
         */
        $field = $this->_getMappedField('conditions_serialized');
        $cCond = $this->_getConditionSql($field, ['like' => $match]);
        $field = $this->_getMappedField('actions_serialized');
        $aCond = $this->_getConditionSql($field, ['like' => $match]);

        $this->getSelect()->where(
            sprintf('(%s OR %s)', $cCond, $aCond),
            null,
            \Magento\Framework\DB\Select::TYPE_CONDITION
        );

        return $this;
    }

    /**
     * Set Priority Sort order
     *
     * @param string $direction
     * @return $this
     */
    public function setPriorityOrder($direction = self::SORT_ORDER_ASC)
    {
        $this->setOrder('sort_order', $direction);
        return $this;
    }

    /**
     * Limit rules collection by date columns
     *
     * @param string $date
     * @return $this
     */
    public function addDateFilter($date = null)
    {
        if ($date == null) {
            $date = $this->date->date()->format('Y-m-d');
        }
        $this->getSelect()->where(
            'from_date IS NULL OR from_date <= ?',
            $date
        )->where(
            'to_date IS NULL OR to_date >= ?',
            $date
        );

        return $this;
    }
}
