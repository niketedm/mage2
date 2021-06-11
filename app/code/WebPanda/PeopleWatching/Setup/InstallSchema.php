<?php
/**
 * @author      WebPanda
 * @package     WebPanda_PeopleWatching
 * @copyright   Copyright (c) WebPanda (https://webpanda-solutions.com/)
 * @license     https://webpanda-solutions.com/license-agreement
 */
namespace WebPanda\PeopleWatching\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'wp_product_view'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('wp_product_view'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product View ID'
            )
            ->addColumn(
                'session_id',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Session ID'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Product ID'
            )
            ->addColumn(
                'viewed_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Viewed At'
            )
            ->addIndex(
                $installer->getIdxName(
                    'wp_product_view',
                    ['session_id', 'product_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['session_id', 'product_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName('wp_product_view', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Product Views');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
