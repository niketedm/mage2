<?php

namespace Synchrony\DigitalBuy\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Insert synchrony promotion rule table
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->addPromotionTable($setup);
        }

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $this->addProductAttributeTable($setup);
        }

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $this->addRuleCustomerGroupRelationTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * Add prmotion table
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addPromotionTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('synchronyrule'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Name'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Description'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Name'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )
            ->addColumn(
                'from_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => true, 'default' => null],
                'From'
            )->addColumn(
                'to_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => true, 'default' => null],
                'To'
            )->addColumn(
                'conditions_serialized',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Conditions Serialized'
            )
            ->addColumn(
                'actions_serialized',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Actions Serialized'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Sort Order'
            )->addIndex(
                $installer->getIdxName('synchronyrule', ['is_active', 'sort_order', 'to_date', 'from_date']),
                ['is_active', 'sort_order', 'to_date', 'from_date']
            )
            ->setComment(
                'Synchrony Promotions'
            );

        $installer->getConnection()->createTable($table);

        $synchronyrulesWebsitesTable = $installer->getTable('synchronyrule_website');
        $websitesTable = $installer->getTable('store_website');

        $table = $installer->getConnection()->newTable(
            $synchronyrulesWebsitesTable
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website Id'
        )->addIndex(
            $installer->getIdxName('synchronyrule ', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $installer->getFkName('synchronyrule_website', 'rule_id', 'synchronyrule', 'rule_id'),
            'rule_id',
            $installer->getTable('synchronyrule'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('synchronyrule_website', 'website_id', 'synchronyrule', 'website_id'),
            'website_id',
            $websitesTable,
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Synchrony Promotion Rules To Websites Relations'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table 'synchronyrule_product_attribute'
     * @param SchemaSetupInterface $setup
     */
    private function addProductAttributeTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $table = $installer->getConnection()->newTable(
            $installer->getTable('synchronyrule_product_attribute')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website Id'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Attribute Id'
        )->addIndex(
            $installer->getIdxName('synchronyrule_product_attribute', ['website_id']),
            ['website_id']
        )->addIndex(
            $installer->getIdxName('synchronyrule_product_attribute', ['attribute_id']),
            ['attribute_id']
        )->addForeignKey(
            $installer->getFkName('synchronyrule_product_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $installer->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('synchronyrule_product_attribute', 'rule_id', 'synchronyrule', 'rule_id'),
            'rule_id',
            $installer->getTable('synchronyrule'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('synchronyrule_product_attribute', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Synchrony Promotion Product Attribute'
        );
        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table 'synchronyrule_customer_group'
     * @param SchemaSetupInterface $setup
     */
    private function addRuleCustomerGroupRelationTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $synchronyRulesCustomerGroupsTable = $installer->getTable('synchronyrule_customer_group');
        $customerGroupTable = $setup->getConnection()->describeTable($setup->getTable('customer_group'));
        $customerGroupIdType = $customerGroupTable['customer_group_id']['DATA_TYPE'] == 'int'
            ? \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER : $customerGroupTable['customer_group_id']['DATA_TYPE'];

        $table = $installer->getConnection()->newTable(
            $synchronyRulesCustomerGroupsTable
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'customer_group_id',
            $customerGroupIdType,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group Id'
        )->addIndex(
            $installer->getIdxName('synchronyrule_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName('synchronyrule_customer_group', 'rule_id', 'synchronyrule', 'rule_id'),
            'rule_id',
            $installer->getTable('synchronyrule'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'synchronyrule_customer_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            ),
            'customer_group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Synchrony Rules To Customer Groups Relations'
        );

        $installer->getConnection()->createTable($table);
    }
}
