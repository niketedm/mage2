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



namespace Mirasvit\Gdpr\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\Gdpr\Api\Data\RequestInterface;

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
            $installer->getTable(RequestInterface::TABLE_NAME)
        )->addColumn(
            RequestInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            RequestInterface::ID
        )->addColumn(
            RequestInterface::CUSTOMER_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            RequestInterface::CUSTOMER_ID
        )->addColumn(
            RequestInterface::TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            RequestInterface::TYPE
        )->addColumn(
            RequestInterface::STATUS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            RequestInterface::STATUS
        )->addColumn(
            RequestInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            RequestInterface::CREATED_AT
        );

        $connection->createTable($table);
    }
}
