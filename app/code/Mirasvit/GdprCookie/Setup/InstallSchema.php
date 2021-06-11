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



namespace Mirasvit\GdprCookie\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\GdprCookie\Api\Data\CookieConsentInterface;
use Mirasvit\GdprCookie\Api\Data\CookieGroupInterface;
use Mirasvit\GdprCookie\Api\Data\CookieInterface;
use Mirasvit\GdprCookie\Repository\CookieGroupRepository;

class InstallSchema implements InstallSchemaInterface
{

    private $cookieGroupRepository;

    public function __construct(
        CookieGroupRepository $cookieGroupRepository
    ) {
        $this->cookieGroupRepository = $cookieGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        $this->createCookieTable($installer);
        $this->createCookieGroupTable($installer);
        $this->createCookieConsentTable($installer);

        $installer->endSetup();
    }

    private function createCookieTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table      = $connection->newTable(
            $installer->getTable(CookieInterface::TABLE_NAME)
        )->addColumn(
            CookieInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            CookieInterface::ID
        )->addColumn(
            CookieInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            CookieInterface::NAME
        )->addColumn(
            CookieInterface::CODE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            CookieInterface::CODE
        )->addColumn(
            CookieInterface::IS_ACTIVE,
            Table::TYPE_SMALLINT,
            1,
            ['nullable' => true],
            CookieInterface::IS_ACTIVE
        )->addColumn(
            CookieInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            CookieInterface::DESCRIPTION
        )->addColumn(
            CookieInterface::LIFETIME,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            CookieInterface::LIFETIME
        )->addColumn(
            CookieInterface::GROUP_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            CookieInterface::GROUP_ID
        )->addColumn(
            CookieInterface::STORE_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            CookieInterface::STORE_IDS
        );
        $connection->dropTable($installer->getTable(CookieInterface::TABLE_NAME));
        $connection->createTable($table);

    }

    private function createCookieGroupTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        $table = $connection->newTable(
            $installer->getTable(CookieGroupInterface::TABLE_NAME)
        )->addColumn(
            CookieGroupInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            CookieGroupInterface::ID
        )->addColumn(
            CookieGroupInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            CookieGroupInterface::NAME
        )->addColumn(
            CookieGroupInterface::PRIORITY,
            Table::TYPE_SMALLINT,
            11,
            ['nullable' => true],
            CookieGroupInterface::PRIORITY
        )->addColumn(
            CookieGroupInterface::IS_ACTIVE,
            Table::TYPE_SMALLINT,
            1,
            ['nullable' => true],
            CookieGroupInterface::IS_ACTIVE
        )->addColumn(
            CookieGroupInterface::IS_REQUIRED,
            Table::TYPE_SMALLINT,
            1,
            ['nullable' => true],
            CookieGroupInterface::IS_REQUIRED
        )->addColumn(
            CookieGroupInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            CookieGroupInterface::DESCRIPTION
        )->addColumn(
            CookieGroupInterface::STORE_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            CookieGroupInterface::STORE_IDS
        );

        $connection->dropTable($installer->getTable(CookieGroupInterface::TABLE_NAME));
        $connection->createTable($table);
    }

    private function createCookieConsentTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        $table = $connection->newTable(
            $installer->getTable(CookieConsentInterface::TABLE_NAME)
        )->addColumn(
            CookieConsentInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            CookieConsentInterface::ID
        )->addColumn(
            CookieConsentInterface::CUSTOMER_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            CookieConsentInterface::CUSTOMER_ID
        )->addColumn(
            CookieConsentInterface::STORE_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            CookieConsentInterface::STORE_ID
        )->addColumn(
            CookieConsentInterface::REMOTE_ADDR,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            CookieConsentInterface::REMOTE_ADDR
        )->addColumn(
            CookieConsentInterface::GROUP_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            CookieConsentInterface::GROUP_IDS
        )->addColumn(
            CookieConsentInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            CookieConsentInterface::CREATED_AT
        );
        $connection->dropTable($installer->getTable(CookieConsentInterface::TABLE_NAME));
        $connection->createTable($table);
    }
}
