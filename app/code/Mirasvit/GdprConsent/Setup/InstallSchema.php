<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gdpr
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\GdprConsent\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\GdprConsent\Api\Data\ConsentInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $connection = $installer->getConnection();

        $installer->startSetup();

        $table = $connection->newTable(
            $installer->getTable(ConsentInterface::TABLE_NAME)
        )->addColumn(
            ConsentInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            ConsentInterface::ID
        )->addColumn(
            ConsentInterface::CUSTOMER_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            ConsentInterface::CUSTOMER_ID
        )->addColumn(
            ConsentInterface::REMOTE_ADDR,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            ConsentInterface::REMOTE_ADDR
        )->addColumn(
            ConsentInterface::TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            ConsentInterface::TYPE
        )->addColumn(
            ConsentInterface::STATUS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            ConsentInterface::STATUS
        )->addColumn(
            ConsentInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            ConsentInterface::CREATED_AT
        );

        $connection->dropTable($setup->getTable(ConsentInterface::TABLE_NAME));
        $connection->createTable($table);
        $installer->endSetup();
    }
}
