<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\RelatedProducts\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Blog schema update
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        $version = $context->getVersion();
        $connection = $setup->getConnection();

        if (version_compare($version, '1.0.2') < 0) {
            /* Add layout field to posts and category table */

                $connection->addColumn(
                    $setup->getTable('magefan_blog_category'),
                    'related_products',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       // 'length' => 255,
                        'nullable' => true,
                        'comment' => 'Related Products',
                    ]
                );




        }

        if (version_compare($version, '1.0.3') < 0) {
            $tableName = $setup->getTable('magefan_blog_category');
            $connection->addColumn(
                $tableName,
                'rp_conditions_serialized',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '2M',
                    'comment' => 'Related Products Rules (Blog+)',
                ]
            );
            $connection->addColumn(
                $tableName,
                'rp_conditions_generation_time',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'comment' => 'Related Products Rules Last Run (Blog+)',
                ]
            );
        }

        $setup->endSetup();
    }
}
