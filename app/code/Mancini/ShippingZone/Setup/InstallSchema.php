<?php

namespace Mancini\ShippingZone\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'shipping_zone'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shipping_zone')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'zone_name',
            Table::TYPE_TEXT,
            250,
            ['nullable' => false],
            'Zone Name'
        )->addColumn(
            'standard_shipping_cost',
            Table::TYPE_DECIMAL,
            '12,4',
            [ 'nullable' => true, 'default' => 0],
            'Standard Shipping Cost'
        )->addColumn(
            'premium_shipping_cost',
            Table::TYPE_DECIMAL,
            '12,4',
            [ 'nullable' => true, 'default' => 99],
            'Premium Shipping Cost'
        )->setComment(
            'Shipping Zone'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'shipping_zone_zipcodes'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shipping_zone_zipcodes')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'zone_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Zone Id'
        )->addColumn(
            'zipcode',
            Table::TYPE_TEXT,
            250,
            ['nullable' => false],
            'Zip Code'
        )->addColumn(
            'city',
            Table::TYPE_TEXT,
            250,
            [ 'nullable' => true],
            'City'
        )->addColumn(
            'state',
            Table::TYPE_TEXT,
            250,
            [ 'nullable' => true],
            'State'
        )->addIndex(
            $setup->getIdxName('shipping_zone_zipcodes', ['zone_id']),
            ['zone_id']
        )->addForeignKey(
            $setup->getFkName('shipping_zone_zipcodes', 'zone_id', 'shipping_zone', 'id'),
            'zone_id',
            $setup->getTable('shipping_zone'),
            'id',
            Table::ACTION_CASCADE
        )->setComment(
            'Shipping Zone Zipcodes'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
