<?php

namespace Synchrony\DigitalBuy\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Rule\Model\ResourceModel\AbstractResource;
use Synchrony\DigitalBuy\Api\Data\RuleInterface;

class Rule extends AbstractResource
{
    /**
     * Associated entities
     */
    const ASSOCIATED_ENTITIES_MAP = [
        'website' => [
            'associations_table' => 'synchronyrule_website',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'website_id',
        ],
        'customer_group' => [
            'associations_table' => 'synchronyrule_customer_group',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'customer_group_id',
        ]
    ];

    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = self::ASSOCIATED_ENTITIES_MAP;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     * @param Json $serializer Optional parameter for backward compatibility
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null,
        Json $serializer = null
    ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $connectionName);
    }
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('synchronyrule', 'rule_id');
    }

    /**
     * Add website ids to rule data after load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $object->setData('website_ids', (array)$this->getWebsiteIds($object->getId()));
        $object->setData('customer_group_ids', (array)$this->getCustomerGroupIds($object->getId()));

        return parent::_afterLoad($object);
    }

    /**
     * Bind synchrony rule to website(s) and product attibutes.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->hasWebsiteIds()) {
            $websiteIds = $object->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($object->getId(), $websiteIds, 'website');
        }

        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        // Save product attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->getProductAttributes($this->serializer->serialize($object->getConditions()->asArray())),
            $this->getProductAttributes($this->serializer->serialize($object->getActions()->asArray()))
        );

        if (count($ruleProductAttributes)) {
            $this->setActualProductAttributes($object, $ruleProductAttributes);
        }

        return parent::_afterSave($object);
    }

    /**
     * Return codes of all product attributes currently used in promo rules
     *
     * @return array
     */
    public function getActiveAttributes()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['a' => $this->getTable('synchronyrule_product_attribute')],
            new \Zend_Db_Expr('DISTINCT ea.attribute_code')
        )->joinInner(
            ['ea' => $this->getTable('eav_attribute')],
            'ea.attribute_id = a.attribute_id',
            []
        )->joinInner(
            ['rule' => $this->getMainTable()],
            'rule.' . $this->getIdFieldName() . ' = a.rule_id',
            []
        );
        return $connection->fetchAll($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of rule
     *
     * @param \Synchrony\DigitalBuy\Model\Rule $rule
     * @param mixed $attributes
     * @return $this
     */
    public function setActualProductAttributes($rule, $attributes)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('synchronyrule_product_attribute'),
            ['rule_id=?' => $rule->getId()]
        );

        //Getting attribute IDs for attribute codes
        $attributeIds = [];
        $select = $this->getConnection()->select()->from(
            ['a' => $this->getTable('eav_attribute')],
            ['a.attribute_id']
        )->where(
            'a.attribute_code IN (?)',
            [$attributes]
        );
        $attributesFound = $this->getConnection()->fetchAll($select);
        if ($attributesFound) {
            foreach ($attributesFound as $attribute) {
                $attributeIds[] = $attribute['attribute_id'];
            }

            $data = [];
            foreach ($rule->getWebsiteIds() as $websiteId) {
                foreach ($attributeIds as $attribute) {
                    $data[] = [
                        'rule_id' => $rule->getId(),
                        'website_id' => $websiteId,
                        'attribute_id' => $attribute,
                    ];
                }
            }
            $connection->insertMultiple($this->getTable('synchronyrule_product_attribute'), $data);
        }

        return $this;
    }

    /**
     * Collect all product attributes used in serialized rule's action or condition
     *
     * @param string $serializedString
     * @return array
     */
    public function getProductAttributes($serializedString)
    {
        // we need 4 backslashes to match 1 in regexp, see http://www.php.net/manual/en/regexp.reference.escape.php
        preg_match_all(
            '~"Synchrony\\\\\\\\DigitalBuy\\\\\\\\Model\\\\\\\\Rule\\\\\\\\Condition\\\\\\\\Product","attribute":"(.*?)"~',
            $serializedString,
            $matches
        );
        // we always have $matches like [[],[]]
        return array_values($matches[1]);
    }
}
