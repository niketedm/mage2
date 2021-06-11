<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AlsoBought
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FrequentlyBought\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mageplaza\FrequentlyBought\Helper\Data;

/**
 * Class UpgradeSchema
 * @package Mageplaza\FrequentlyBought\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * UpgradeSchema constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($this->helper->versionCompare('2.3.0')) {
            return $this;
        }

        $installer = $setup;
        $installer->startSetup();

        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $table = $connection->newTable($installer->getTable('mageplaza_fbt_product_link'))
                ->addColumn('link_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ], 'Link ID')
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Product ID'
                )
                ->addColumn(
                    'linked_product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Linked Product ID'
                )
                ->addColumn(
                    'position',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Position'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'mageplaza_fbt_product_link',
                        ['product_id', 'linked_product_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['product_id', 'linked_product_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $installer->getIdxName('mageplaza_fbt_product_link', ['product_id']),
                    ['product_id']
                )
                ->addIndex(
                    $installer->getIdxName('mageplaza_fbt_product_link', ['linked_product_id']),
                    ['linked_product_id']
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_fbt_product_link',
                        'product_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_fbt_product_link',
                        'linked_product_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'linked_product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Frequently Bought Together Product To Product Linkage Table');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
